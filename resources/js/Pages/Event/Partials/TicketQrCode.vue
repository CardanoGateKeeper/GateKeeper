<script setup>
import {onMounted, ref, watch} from "vue";
import QRCodeStyling from 'styled-qr-code';

const props = defineProps({
  data:  {
    type:     String,
    required: false
  },
  image: {
    type:     String,
    required: false
  }
});

const qr_code = ref(null);
const qr_options = {
  width:                512,
  height:               512,
  type:                 'canvas',
  margin:               4,
  qrOptions:            {
    typeNumber:           0,
    mode:                 'Byte',
    errorCorrectionlevel: 'L'
  },
  imageOptions:         {
    hideBackgroundDots: true,
    imageSize:          1,
    margin:             2,
    // crossOrigin:        false,
    crossOrigin:        'anonymous'
  },
  dotsOptions:          {
    // type:  'rounded',
    color: '#000000',
  },
  backgroundOptions:    {
    color: "#ffffff"
  },
  cornersSquareOptions: {
    type:  "extra-rounded",
    color: "#000000"
  },
  cornersDotOptions:    {
    type:  "dot",
    color: "#000000"
  },
};

const qrCode = new QRCodeStyling(qr_options);

const doDownload = () => {
  qrCode.download({
    name:      'my_ticket',
    extension: 'png',
  }, 1);
}

onMounted(async () => {
  qrCode.append(qr_code.value)
});

watch(() => props, async (newValue) => {
  // const timestamp = new Date().getTime();
  // const img_path = `${newValue.image}?timestamp=${timestamp}`;
  const data_uri = newValue.data;

  qrCode.update({
    ...qr_options,
    data:  data_uri,
    // image: img_path
  });

  qrCode.append(qr_code.value);
}, {deep: true, immediate: true});

</script>

<template>
  <div ref="qr_code"/>
    <v-btn color="primary" @click="doDownload">Download</v-btn>
</template>

<style>
canvas {
  max-width: 100%
}
</style>
