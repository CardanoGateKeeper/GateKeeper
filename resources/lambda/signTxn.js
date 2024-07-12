const CSL = require('@emurgo/cardano-serialization-lib-nodejs');

const authenticate_transaction = ({tx_hex, stake_key, nonce}) => {
  const resp_object = {
    valid_transaction: false,
    valid_signature:   false,
    valid_nonce:       false,
  }

  const stake_key_hex = CSL.Address.from_bech32(stake_key)
                           .to_hex()
                           .substring(2);
  const tx = CSL.Transaction.from_bytes(Buffer.from(tx_hex, 'hex'));
  const witnesses = tx.witness_set();
  const tx_hash = CSL.hash_transaction(tx.body())
                     .to_hex();
  const tx_metadata_hash = tx.body()
                             .auxiliary_data_hash()
                             .to_hex();
  const test_metadata_hash = CSL.hash_auxiliary_data(tx.auxiliary_data())
                                .to_hex();
  const nonce_entry_value = tx.auxiliary_data()
                              .metadata()
                              .get(CSL.BigNum.from_str('8'));
  let nonce_val = '';

  for (let i = 0; i < nonce_entry_value.as_list()
                                       .len(); i++) {
    nonce_val += nonce_entry_value.as_list()
                                  .get(i)
                                  .as_text();
  }

  resp_object.valid_nonce = (nonce_val === nonce && test_metadata_hash === tx_metadata_hash);
  resp_object.valid_transaction = tx.is_valid();

  for (let i = 0; i < witnesses.vkeys()
                               .len(); i++) {
    const witness = witnesses.vkeys()
                             .get(i);
    const WitnessVkey = witness.vkey();
    const WitnessPubKey = WitnessVkey.public_key();
    if (WitnessPubKey.hash()
                     .to_hex() === stake_key_hex) {
      resp_object.valid_signature = WitnessPubKey.verify(Buffer.from(tx_hash, 'hex'), witness.signature());
    }
  }

  return (resp_object.valid_transaction && resp_object.valid_signature && resp_object.valid_nonce);
}

exports.validate = async function (event) {
  return authenticate_transaction({
    tx_hex:    event.tx_hex,
    stake_key: event.stake_key,
    nonce:     event.nonce,
  });
}
