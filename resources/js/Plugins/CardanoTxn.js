import {
  BigNum,
  ExUnitPrices,
  LinearFee,
  TransactionBuilderConfigBuilder,
  TransactionBuilder,
  UnitInterval
} from "@emurgo/cardano-serialization-lib-asmjs";

export default {
  /**
   *
   * @param Object parameters
   * @returns TransactionBuilder
   */
  prepare({parameters}) {
    const txBuilderConfig = TransactionBuilderConfigBuilder
      .new()
      .fee_algo(
        LinearFee.new(
          BigNum.from_str(
            parameters.linearFee.minFeeA.toString()
          ),
          BigNum.from_str(
            parameters.linearFee.minFeeB.toString()
          )
        )
      )
      .coins_per_utxo_byte(
        BigNum.from_str(parameters.costPerWord.toString())
      )
      .pool_deposit(
        BigNum.from_str(parameters.poolDeposit.toString())
      )
      .key_deposit(
        BigNum.from_str(parameters.keyDeposit.toString())
      )
      .max_value_size(parameters.maxValSize)
      .max_tx_size(parameters.maxTxSize)
      .ex_unit_prices(
        ExUnitPrices.new(
          UnitInterval.new(
            BigNum.from_str("1"),
            BigNum.from_str("1")
          ),
          UnitInterval.new(
            BigNum.from_str("1"),
            BigNum.from_str("1")
          )
        )
      )
      .build();

    return TransactionBuilder.new(txBuilderConfig);
  },

}
