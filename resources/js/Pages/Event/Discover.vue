<script setup>
import {Buffer} from "buffer";
import {
  Address,
  Value,
  ScriptHash,
} from "@emurgo/cardano-serialization-lib-asmjs";
import {onMounted, ref, reactive, computed} from "vue";
import {useTheme} from "vuetify";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import axios from "axios";
import AppHeader from "@/Components/AppHeader.vue";

defineProps({
  canLogin: {
    type: Boolean,
  },
  canRegister: {
    type: Boolean,
  }
});

const bg_image = 'url(https://picsum.photos/2048/1080)';

const snackbar = reactive({
  show: false,
  message: "",
  color: "info", // 'success' | 'warning' | 'error' | 'info'
  timeout: 6000,
});

function showSnackbar(message, color = "info", timeout = 6000) {
  snackbar.message = message;
  snackbar.color = color;
  snackbar.timeout = timeout;
  snackbar.show = true;
}

const modal = ref({
  connectWallet: false,
});

const cardano = ref({
  hasCardano: ref(false),
  loading: ref(true),
  attempts: ref(10),
  status: ref("loading"),
  wallets: [],
  connected: null,
  connection: null,
  hardware_mode: ref(false),
  network_mode: null,
});

const allEvents = ref([]);
const eventsLoading = ref(false);
const walletPolicies = ref([]);

const eligibleEvents = computed(() => {
  if (!walletPolicies.value.length) {
    return [];
  }

  return allEvents.value.filter((event) => {
    const hashes = event.policy_hashes || [];
    return hashes.some((hash) => walletPolicies.value.includes(hash));
  });
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

  if (wallet.experimental && wallet.experimental.vespr_compat === true) {
    return false;
  }

  if (wallet.name.includes("via VESPR")) {
    return false;
  }

  return true;
};

const format_wallet_name = (wallet) => {
  let formatted_name;

  formatted_name = wallet.name.toLowerCase();
  formatted_name = formatted_name.replace(" wallet", "");

  return formatted_name;
};

const find_wallets = () => {
  const target_wallet = localStorage.getItem('connected_wallet');
  let loop = setInterval(() => {
    if (cardano.value.attempts <= 0) {
      if (cardano.value.wallets.length) {
        cardano.value.status = "found";
      } else {
        cardano.value.status = "notfound";
      }
      clearInterval(loop);
      cardano.value.loading = false;
      return;
    }

    if (window.cardano !== undefined) {
      cardano.value.hasCardano = true;

      Object.keys(window.cardano).forEach(async (name) => {
        if (!is_valid_wallet(name)) {
          return;
        }

        const wallet = window.cardano[name];
        if (wallet.name === target_wallet && !cardano.value.connected) {
          await connect(wallet);
        }

        if (!cardano.value.wallets.includes(wallet)) {
          cardano.value.wallets.push(wallet);
        }
      });
    }

    cardano.value.attempts--;
  }, 250);
};

const connect = async (wallet) => {
  wallet.busy = true;
  try {
    cardano.value.connection = await wallet.enable();
  } catch (e) {
    wallet.busy = false;
    return;
  }
  localStorage.setItem('connected_wallet', wallet.name);
  cardano.value.connected = wallet;
  wallet.busy = false;
  modal.value.connectWallet = false;
  cardano.value.network_mode = await cardano.value.connection.getNetworkId();

  await check_balance();
};

const disconnect = () => {
  cardano.value.connected = null;
  cardano.value.connection = null;
  cardano.value.network_mode = null;
  walletPolicies.value = [];
  localStorage.removeItem('connected_wallet');
};

const check_balance = async () => {
  const wallet = cardano.value.connected;
  const api = cardano.value.connection;
  if (!wallet || !api) {
    return;
  }

  wallet.busy = true;
  wallet.balance = null;
  wallet.assets = {};
  walletPolicies.value = [];

  try {
    wallet.balance = Value.from_hex(await api.getBalance());
  } catch (e) {
    console.error(`Error getting wallet balance?`, e);
    showSnackbar("Unable to read wallet balance.", "error");
    wallet.busy = false;
    return;
  }

  const multi = wallet.balance.multiasset();
  if (!multi) {
    wallet.busy = false;
    return;
  }

  const policies = multi.keys();
  const policySet = new Set();

  for (let i = 0; i < policies.len(); i++) {
    const policyHash = policies.get(i);
    let policyHex;
    try {
      policyHex = toHex(policyHash.to_bytes());
    } catch (e) {
      console.error(`Unable to convert policy hash to hex`, e);
      continue;
    }
    policySet.add(policyHex);
  }

  walletPolicies.value = Array.from(policySet);
  wallet.busy = false;
};

const loadEvents = async () => {
  eventsLoading.value = true;

  try {
    const response = await axios.get('/api/events/discover');
    allEvents.value = response.data.data || [];
  } catch (e) {
    console.error('Error loading events', e);
    showSnackbar(
      e?.response?.data?.message ||
      'There was an error while loading public events. Please try again.',
      'error'
    );
  } finally {
    eventsLoading.value = false;
  }
};

const fromHex = (string) => {
  return Buffer.from(string, "hex");
};

const toHex = (bytes) => {
  return Buffer.from(bytes).toString("hex");
};

// --- Theme handling (same pattern as Show.vue) ---

const theme = useTheme();

const localTheme = ref({
  value: null,
});

const toggleTheme = () => {
  const theme_value = theme.global.current.value.dark ? "light" : "dark";
  localStorage.setItem("gatekeeper:theme", theme_value);
  theme.global.name.value = theme_value;
};

onMounted(async () => {
  find_wallets();
  localTheme.value = localStorage.getItem("gatekeeper:theme") ?? "light";
  theme.global.name.value = localTheme.value;
  await loadEvents();
});
</script>

<template>
  <GuestLayout title="Discover Events">
    <template #header>
      <header class="discover-header px-8">
        <AppHeader/>
        <div class="py-16 d-flex align-center my-4">
          <div>
            <h1>Discover Events</h1>
            <p class="mb-2">
              Connect your Cardano wallet to see which public events you’re
              eligible to attend based on the NFTs and tokens you hold.
            </p>
          </div>
          <v-spacer/>
          <template v-if="cardano.connected === null">
            <v-btn
              color="primary"
              size="large"
              :loading="cardano.loading"
              v-if="cardano.status != 'notfound'"
              @click="modal.connectWallet = true"
            >
              Connect Wallet
            </v-btn>
          </template>
          <template v-else>
            <div class="d-flex flex-column align-end">
              <div>
                <v-btn size="large" class="me-2"
                       :loading="cardano.connected.busy">
                  <v-avatar size="24" class="me-2">
                    <v-img :src="cardano.connected.icon"/>
                  </v-avatar>
                  {{ format_wallet_name(cardano.connected) }} Connected
                </v-btn>
                <v-btn
                  size="large"
                  :loading="cardano.connected.busy"
                  class="me-2"
                  @click="check_balance().then(discoverEvents)"
                >
                  <v-icon icon="mdi-reload"/>
                </v-btn>
                <v-btn
                  size="large"
                  :loading="cardano.connected.busy"
                  @click="disconnect"
                >
                  <v-icon icon="mdi-power"/>
                </v-btn>
              </div>
              <small class="mt-2">
                Found {{ walletPolicies.length }} unique policy
                {{ walletPolicies.length === 1 ? "ID" : "IDs" }} in your wallet.
              </small>
            </div>
          </template>
        </div>
      </header>
    </template>

    <v-container fluid>
      <v-alert v-if="cardano.status == 'notfound'" type="error" class="mb-4">
        <v-alert-title>No Cardano Wallet Found!</v-alert-title>
        <p>
          To discover eligible events, you’ll need a browser wallet that
          supports the Cardano CIP-30 wallet standard. Please install a
          compatible wallet and reload this page.
        </p>
      </v-alert>

      <v-progress-linear
        color="primary"
        height="12"
        indeterminate
        v-if="cardano.loading || eventsLoading"
        class="mb-4"
      />

      <template v-if="cardano.connected && !eventsLoading">
        <template v-if="eligibleEvents.length">
          <h2 class="mb-4">Events matching your wallet</h2>
          <v-row>
            <v-col
              cols="12"
              md="6"
              lg="4"
              v-for="event in eligibleEvents"
              :key="event.uuid"
            >
              <v-card>
                <v-card-title>{{ event.name }}</v-card-title>
                <v-card-subtitle>
                  <div v-if="event.location">
                    <v-icon icon="mdi-map-marker" class="me-1"/>
                    {{ event.location }}
                  </div>
                  <div v-if="event.date">
                    <v-icon icon="mdi-calendar" class="me-1"/>
                    {{ event.date }}
                  </div>
                  <div v-if="event.start || event.end">
                    <v-icon icon="mdi-clock-outline" class="me-1"/>
                    <span v-if="event.start && event.end">
                      {{ event.start }} – {{ event.end }}
                    </span>
                    <span v-else-if="event.start">
                      Starts at {{ event.start }}
                    </span>
                    <span v-else-if="event.end">
                      Until {{ event.end }}
                    </span>
                  </div>
                </v-card-subtitle>
                <v-card-text>
                  <p v-if="event.description" class="mb-4">
                    {{ event.description }}
                  </p>

                  <div v-if="event.policies && event.policies.length">
                    <p class="mb-2">
                      You qualify via the following policy
                      {{ event.policies.length === 1 ? "ID" : "IDs" }}:
                    </p>
                    <v-chip-group column>
                      <v-chip
                        v-for="policy in event.policies"
                        :key="policy.hash"
                        color="primary"
                        variant="outlined"
                        class="mb-1"
                      >
                        {{ policy.name || policy.hash }}
                      </v-chip>
                    </v-chip-group>
                  </div>
                </v-card-text>
                <v-card-actions>
                  <v-btn
                    color="primary"
                    variant="text"
                    :href="route('event.show', event.uuid)"
                  >
                    View Event
                  </v-btn>
                </v-card-actions>
              </v-card>
            </v-col>
          </v-row>
        </template>

        <v-alert
          v-else
          type="info"
          class="mt-4"
        >
          <template v-if="allEvents.length">
            No matching public events were found for the assets in your wallet.
            Try again later or connect a different wallet.
          </template>
          <template v-else>
            There are currently no public upcoming events configured in
            GateKeeper.
          </template>
        </v-alert>
      </template>

      <template v-else-if="!cardano.loading && cardano.connected === null">
        <v-alert type="info">
          Connect a compatible Cardano wallet to discover eligible events.
        </v-alert>
      </template>
    </v-container>

    <!-- Connect Wallet Dialog -->
    <v-dialog v-model="modal.connectWallet" persistent scrollable
              max-width="512">
      <v-card>
        <v-card-title class="d-flex align-center">
          Connect Wallet
          <v-spacer/>
          <v-btn icon="mdi-close" @click="modal.connectWallet = false"/>
        </v-card-title>
        <v-card-text>
          <v-btn
            :loading="wallet.busy"
            block
            v-for="wallet in cardano.wallets"
            :key="wallet.name"
            class="mb-2"
            size="large"
            color="primary"
            @click="connect(wallet)"
          >
            <v-avatar size="24" class="me-2">
              <v-img :src="wallet.icon"/>
            </v-avatar>
            {{ format_wallet_name(wallet) }}
          </v-btn>
        </v-card-text>
      </v-card>
    </v-dialog>

    <!-- Local snackbar for API / wallet errors -->
    <v-snackbar
      v-model="snackbar.show"
      :color="snackbar.color"
      :timeout="snackbar.timeout"
      location="top end"
      variant="elevated"
    >
      <div class="d-flex align-center justify-space-between ga-4">
        <span>{{ snackbar.message }}</span>

        <v-btn
          icon="mdi-close"
          size="small"
          variant="text"
          @click="snackbar.show = false"
        />
      </div>
    </v-snackbar>
  </GuestLayout>
</template>
<style>
header.discover-header {
  color: white;
  background: linear-gradient(to right, rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0.3)),
  v-bind('bg_image'),
  linear-gradient(rgba(114, 76, 195, 1.0),
    rgba(114, 76, 195, 1.0)) center center;
  background-size: cover;

}
</style>
