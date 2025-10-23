<script setup>
import {Buffer} from "buffer";
import {
  Address,
  BigNum,
  Certificate,
  Certificates,
  Ed25519KeyHash,
  MetadataList,
  RewardAddress,
  ScriptHash,
  StakeCredential,
  StakeDelegation, Transaction,
  TransactionMetadatum, TransactionWitnessSet,
  Value, Vkeywitnesses
} from "@emurgo/cardano-serialization-lib-asmjs";
import {onMounted, ref, watch} from 'vue'
import {useTheme} from "vuetify";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import {bech32} from "bech32";
import blake2b from "blake2b";
import Koios from "@/Plugins/Koios.js";
import CardanoTxn from "@/Plugins/CardanoTxn.js";
import TicketQrCode from "@/Pages/Event/Partials/TicketQrCode.vue";

const koios_token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhZGRyIjoic3Rha2UxdXk1Nm5uN3c1OGRyNWsyOG1mcnhnaHBuZ25uNHo0N2pkcGdwOW1ldXZncDdrNXFtaHljbnAiLCJleHAiOjE3NTA0NDIxMjksInRpZXIiOjEsInByb2pJRCI6ImdhdGVrZWVwZXJfZGV2ZWxvcG1lbnQifQ.MdJeU_o85Z8OG51cNRijbcdD78S7vmbrXc9pIA6u-oY";

const props = defineProps({
  event: Object,
});

const modal = ref({
  connectWallet: false,
  showTicket:    false
})

const cardano = ref({
  hasCardano:    ref(false),
  loading:       ref(true),
  attempts:      ref(10),
  status:        ref('loading'),
  wallets:       [],
  connected:     null,
  connection:    null,
  hardware_mode: ref(false),
  network_mode:  null,
});

const is_valid_wallet = (name) => {


  if (name === "typhon" || window.cardano[name] === undefined) {
    return false;
  }

  const wallet = window.cardano[name];

  if (wallet.name === undefined || wallet.icon === undefined) {
    return false;
  }

  if (name !== "typhoncip30" && wallet.name.toLowerCase() !== name.toLowerCase()) {
    return false;
  }

  if (
    wallet.experimental &&
    wallet.experimental.vespr_compat === true
  ) {
    return false;
  }

  if (wallet.name.includes("via VESPR")) {
    return false;
  }

  return true;
}

const format_wallet_name = (wallet) => {
  let formatted_name;

  formatted_name = wallet.name.toLowerCase();
  formatted_name = formatted_name.replace(' wallet', '');

  return formatted_name;
}

const find_wallets = () => {
  let loop = setInterval(() => {
    if (cardano.value.attempts <= 0) {
      if (cardano.value.wallets.length) {
        cardano.value.status = 'found';
      } else {
        cardano.value.status = 'notfound';
      }
      clearInterval(loop);
      cardano.value.loading = false;
      return;
    }

    if (window.cardano !== undefined) {
      cardano.value.hasCardano = true;

      Object.keys(window.cardano)
            .forEach((name) => {


              if (!is_valid_wallet(name)) {
                return;
              }

              const wallet = window.cardano[name];

              if (!cardano.value.wallets.includes(wallet)) {
                cardano.value.wallets.push(wallet);
              }
            });

    }

    cardano.value.attempts--;
  }, 250);
}

const connect = async (wallet) => {
  wallet.busy = true;
  try {
    cardano.value.connection = await wallet.enable();
  } catch (e) {
    wallet.busy = false;
    return;
  }
  cardano.value.connected = wallet;
  wallet.busy = false;
  modal.value.connectWallet = false;
  cardano.value.network_mode = await cardano.value.connection.getNetworkId();
  check_balance();
}

const disconnect = () => {
  cardano.value.connected = null;
  cardano.value.connection = null;
  cardano.value.network_mode = null;
}

const check_balance = async () => {
  const wallet = cardano.value.connected;
  const api = cardano.value.connection;
  wallet.busy = true;
  wallet.balance = null;
  wallet.assets = {};

  try {
    wallet.balance = Value.from_hex(
      await api.getBalance()
    );
  } catch (e) {
    console.error(`Error getting wallet balance?`, e);
  }


  props.event.policies.forEach((policy) => {
    wallet.assets[policy.hash] = [];
    const policy_hash = ScriptHash.from_bytes(fromHex(policy.hash));
    const policy_assets = wallet.balance.multiasset()
                                .get(policy_hash);

    if (policy_assets === undefined) {
      return;
    }

    for (let i = 0; i < policy_assets.keys()
                                     .len(); i++) {
      const Asset = policy_assets.keys()
                                 .get(i);
      const AssetName = toAscii(Asset.name());
      const asset = {
        name:      AssetName,
        policy_id: policy.hash,
        asset_id:  toHex(Asset.name()),
      };
      make_fingerprint(asset);
      wallet.assets[policy.hash].push(asset);
    }
  })

  wallet.busy = false;
}

const make_fingerprint = (asset) => {
  const asset_hex = asset.policy_id + asset.asset_id;
  const asset_buffer = Buffer.from(asset_hex, 'hex');
  const b2_hashed = blake2b(20)
    .update(asset_buffer)
    .digest('hex');
  asset.fingerprint = bech32.encode('asset', bech32.toWords(Buffer.from(b2_hashed, 'hex')));
}

const fromHex = (string) => {
  return Buffer.from(string, "hex");
}

const toHex = (bytes) => {
  return Buffer.from(bytes)
               .toString("hex");
}

const toAscii = (bytes) => {
  return Buffer.from(bytes)
               .toString("ascii");
}

const generate_ticket = async (asset) => {
  cardano.value.connected.busy = true;

  const reward_addresses = await cardano.value.connection.getRewardAddresses();
  const stake_address_cbor = reward_addresses[0];
  const stake_key = Address.from_bytes(Buffer.from(stake_address_cbor, 'hex'));
  const stake_bech32 = stake_key.to_bech32(cardano.value.network_mode ? 'stake' : 'stake_test');

  let ticket_nonce;

  try {
    ticket_nonce = await axios.post(route('ticket.store'), {
      event_uuid: props.event.uuid,
      stake_key:  stake_bech32,
      policy_id:  asset.policy_id,
      asset_id:   asset.asset_id
    });

    console.log(`Ticket Nonce:`, ticket_nonce);
  } catch (e) {
    console.error(`Couldn't get a nonce!`, e);
    cardano.value.connected.busy = false;
    return false;
  }

  const nonce = ticket_nonce.data.nonce;
  let signature;

  try {
    if (!cardano.value.hardware_mode) {
      signature = await signData(stake_address_cbor, nonce, asset.policy_id, asset.asset_id);
    } else {
      const txn = await createTxn(stake_key, nonce);
      const witness = await cardano.value.connection.signTx(txn.to_hex(), true);

      const witnessSet = TransactionWitnessSet.new();
      const totalVkeys = Vkeywitnesses.new();
      const addWitness = TransactionWitnessSet.from_bytes(Buffer.from(witness, 'hex'));
      const addVkeys = addWitness.vkeys();
      if (addVkeys) {
        for (let i = 0; i < addVkeys.len(); i++) {
          totalVkeys.add(addVkeys.get(i));
        }
      }

      witnessSet.set_vkeys(totalVkeys);
      const signedTx = Transaction.new(
        txn.body(),
        witnessSet,
        txn.auxiliary_data()
      );


      signature = {
        txn: signedTx.to_hex(),
        witness
      };
    }
  } catch (e) {
    console.error(`Ticket Signing Error!`, e);
  }

  let ticket_validation;

  if (signature) {
    try {
      ticket_validation = await axios.put(route('ticket.update', ticket_nonce.data), {
        event_uuid: props.event.uuid,
        stake_key:  stake_bech32,
        policy_id:  asset.policy_id,
        asset_id:   asset.asset_id,
        nonce,
        signature
      });
    } catch (e) {
      console.error(`Ticket Validation Error!`, e);
    }
  }

  if (ticket_validation) {
    modal.value.showTicket = true;
    qr_image_value = asset.fingerprint ? route('image.show', {asset_key: asset.fingerprint}) : '';
    qr_code_value = ticket_validation.data.qr_value;
  }


  cardano.value.connected.busy = false;
}

let qr_code_value = null;
let qr_image_value = '';

const createTxn = async (stake_key, nonce) => {
  const params = await Koios.getParameters({
    project_id: koios_token
  });
  const txBuilder = CardanoTxn.prepare({parameters: params});

  try {
    const metadata_list = MetadataList.new();
    while (nonce) {
      if (nonce.length < 64) {
        metadata_list.add(TransactionMetadatum.new_text(nonce));
        break;
      } else {
        metadata_list.add(TransactionMetadatum.new_text(nonce.substring(0, 64)));
        nonce = nonce.substring(64);
      }
    }
    txBuilder.add_metadatum(
      BigNum.from_str('8'),
      TransactionMetadatum.new_list(
        metadata_list
      )
    );
  } catch (e) {
    console.error(`Couldn't add metadata?`, e, nonce);
  }

  const reward_address = RewardAddress.from_address(stake_key);
  const reward_keyhash = reward_address.payment_cred()
                                       .to_keyhash();

  const tx_certs = Certificates.new();
  tx_certs.add(
    Certificate.new_stake_delegation(
      StakeDelegation.new(
        StakeCredential.from_keyhash(reward_keyhash),
        Ed25519KeyHash.from_bech32(`pool14wk2m2af7y4gk5uzlsmsunn7d9ppldvcxxa5an9r5ywek8330fg`)
      )
    )
  );

  txBuilder.set_certs(tx_certs);
  txBuilder.set_fee(BigNum.from_str('0'));
  txBuilder.set_ttl(1);

  return txBuilder.build_tx();
}

const signData = async (stake_address, nonce, policy_id, asset_id) => {
  const payload = cardano.value.connection.signData(stake_address, nonce);
  console.log(`Sign Data Payload`, payload);
  return payload;
}

onMounted(async () => {
  find_wallets();
  localTheme.value = localStorage.getItem('gatekeeper:theme') ?? 'light';
  theme.global.name.value = localTheme.value;
});

const theme = useTheme()

const toggleTheme = () => {
  const theme_value = theme.global.current.value.dark ? 'light' : 'dark'
  localStorage.setItem('gatekeeper:theme', theme_value);
  theme.global.name.value = theme_value;
}

const localTheme = ref({
  value: null
});

const bg_image = 'url(' + props.event.bg_image_url + ')';
</script>
<style>
header {
  color: white;
  background: linear-gradient(to right, rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0.3)),
  v-bind('bg_image'),
  linear-gradient(rgba(114, 76, 195, 1.0),
    rgba(114, 76, 195, 1.0)) center center;
  background-size: cover;

}
</style>
<template>
  <GuestLayout title="Show Event">
    <template #header>
      <header class="pb-16 px-8 text-start">
        <v-toolbar class="d-flex flex-row pb-16" color="transparent">
          <v-spacer></v-spacer>
          <v-toolbar-items>
            <v-btn @click="toggleTheme">
              <v-icon
                :icon="theme.global.current.value.dark ? 'mdi-weather-sunny' : 'mdi-weather-night'"/>
            </v-btn>
          </v-toolbar-items>
        </v-toolbar>
        <div class="d-flex align-center align-content-center my-16">
          <v-avatar :image="event.profile_photo_url" class="me-4" size="128"/>
          <div>
            <p>
              <v-avatar :image="event.team.profile_photo_url" size="32"
                        class="me-2"/>
              {{ event.team.name }} presents
            </p>
            <h1>{{ event.name }}</h1>
            <p>
              <span v-if="event.description">
                {{ event.description }}
                <br/>
              </span>
              <span v-if="event.location">
                <v-icon icon="mdi-web" class="me-2"/>
                {{ event.location }}
                <br/>
              </span>
              <span v-if="event.event_date">
                <v-icon icon="mdi-calendar-clock" class="me-2"/>
                {{ event.event_date }}
                <template v-if="event.event_start && event.event_end">
                  from {{ event.event_start }} to {{ event.event_end }}
                </template>
                <template v-else-if="event.event_start && !event.event_end">
                  at {{ event.event_start }}
                </template>
                <template v-else-if="event.event_end && !event.event_start">
                  until {{ event.event_end }}
                </template>
              </span>
            </p>
          </div>
          <v-spacer/>
          <template v-if="cardano.connected === null">
            <v-btn color="primary" size="large" :loading="cardano.loading"
                   v-if="cardano.status != 'notfound'"
                   @click="modal.connectWallet = true">Connect Wallet
            </v-btn>
          </template>
          <template v-else>
            <div class="d-flex flex-column">
              <div>
                <v-btn size="large" class="me-2"
                       :loading="cardano.connected.busy">
                  <v-avatar size="24" class="me-2">
                    <v-img :src="cardano.connected.icon"/>
                  </v-avatar>
                  {{ format_wallet_name(cardano.connected) }} Connected
                </v-btn>
                <v-btn size="large" :loading="cardano.connected.busy"
                       class="me-2"
                       @click="check_balance">
                  <v-icon icon="mdi-reload"/>
                </v-btn>
                <v-btn size="large" :loading="cardano.connected.busy"
                       @click="disconnect">
                  <v-icon icon="mdi-power"/>
                </v-btn>
              </div>
              <br/>
              <v-switch v-model="cardano.hardware_mode" color="primary"
                        density="comfortable">
                <template v-slot:label>
                  Hardware Wallet Compatibility Mode is {{
                    cardano.hardware_mode ? 'ON' : 'OFF'
                  }}
                </template>
              </v-switch>
            </div>

          </template>
        </div>
      </header>
    </template>

    <v-container fluid>
      <v-alert v-if="cardano.status == 'notfound'" type="error"
               class="mb-4">
        <v-alert-title>No Cardano Wallet Found!</v-alert-title>
        <p>
          We're very sorry, you must connect your wallet to show proof of
          ownership in order to generate tickets for this event. Please try
          again later.
        </p>
      </v-alert>

      <v-progress-linear color="primary" height="12" indeterminate
                         v-if="cardano.loading"/>

      <template v-if="cardano.connected && cardano.connected.assets">
        <template v-for="policy in event.policies" :key="policy.hash">
          <div
            v-if="cardano.connected.assets[policy.hash]?.length">
            <h2 class="mb-4">{{ policy.name }} Assets</h2>
            <v-row class="mb-8">
              <v-col cols="6" md="4" lg="3" xl="2"
                     v-for="token in cardano.connected.assets[policy.hash] || []">
                <v-card>
                  <v-card-title>{{ token.name }}</v-card-title>
                  <v-img
                    :src="token.fingerprint ? route('image.show', {asset_key: token.fingerprint}) : ''"/>
                  <v-card-actions>
                    <v-btn color="primary" @click="generate_ticket(token)">
                      Generate
                      Ticket
                    </v-btn>
                  </v-card-actions>
                </v-card>
              </v-col>
            </v-row>
          </div>
        </template>
      </template>
    </v-container>
    <v-dialog v-model="modal.connectWallet" persistent scrollable
              max-width="512">
      <v-card>
        <v-card-title class="d-flex align-center">
          Connect Wallet
          <v-spacer/>
          <v-btn icon="mdi-close" @click="modal.connectWallet = false"/>
        </v-card-title>
        <v-card-text>
          <v-btn :loading="wallet.busy" block
                 v-for="wallet in cardano.wallets" :key="wallet.name"
                 class="mb-2" size="large" color="primary"
                 @click="connect(wallet)">
            <v-avatar size="24" class="me-2">
              <v-img :src="wallet.icon"/>
            </v-avatar>
            {{ format_wallet_name(wallet) }}
          </v-btn>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog v-model="modal.showTicket" persistent scrollable max-width="560">
      <v-card>
        <v-card-title class="d-flex align-center">
          Your Ticket
          <v-spacer/>
          <v-btn icon="mdi-close" @click="modal.showTicket = false"/>
        </v-card-title>
        <v-card-text class="text-center">
          <TicketQrCode :data="qr_code_value" :image="qr_image_value"/>
        </v-card-text>
        <v-card-text>
          <v-alert type="info">
            The QR code shown above is your ticket! Please print it out or save
            it to your mobile device to be shown at the door when you check in!
          </v-alert>
        </v-card-text>
        <v-card-actions>
          <v-btn @click="modal.showTicket = false">Close</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </GuestLayout>
</template>
