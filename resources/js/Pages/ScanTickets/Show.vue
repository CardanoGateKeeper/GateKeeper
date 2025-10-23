<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import {onMounted, ref} from "vue";
import {QrcodeStream} from 'qrcode-reader-vue3'
import {router} from "@inertiajs/vue3";

const props = defineProps({
  event: Object
});

const scanner = ref(null);

const scanner_options = ref({
  status:  0,
  mode:    'checkin',
  message: null,
})

const doDecode = (result) => {
  console.log(`Decoded!`, result);
  scanner_options.value.status = 1;
  const [asset_id, ticket_code] = result.split('|');
  if (scanner_options.value.mode === 'checkin') {
    scanner_options.value.message = "Checking in the ticket!";
    try {
      doCheckin({asset_id, ticket_code});
    } catch (e) {
      console.error(`Check-in error`, e);
    }
  } else {
    scanner_options.value.message = "Checking out the ticket!";
    try {
      doCheckout({asset_id, ticket_code});
    } catch (e) {
      console.error(`Check-out error`, e);
    }
  }
}

const doCheckin = async ({asset_id, ticket_code}) => {
  let response;
  try {
    response = await axios.post(route('event.check-in.store', {event: props.event.uuid}), {
      asset_id,
      ticket_code
    });
  } catch (e) {
    scanner_options.value.status = 3;
    scanner_options.value.message = e.response.data.message;
    console.error(`Check-in error`, e);
    return;
  }

  if (response.data.id) {
    scanner_options.value.status = 2;
    scanner_options.value.message = "Ticket checked in!";
  } else {
    console.error('Error checking in?', response);
  }

  console.log(`Checkin Response`, response);
}

const doCheckout = async ({asset_id, ticket_code}) => {
  let response;
  try {
    response = await axios.post(route('event.check-out.store', {event: props.event.uuid}), {
      asset_id,
      ticket_code
    });
  } catch (e) {
    scanner_options.value.status = 3;
    scanner_options.value.message = e.response.data.message;
    console.error(`Check-out error`, e);
    return;
  }

  if (response.data.id) {
    scanner_options.value.status = 2;
    scanner_options.value.message = "Ticket checked out!";
  } else {
    console.error('Error checking out?', response);
  }

  console.log(`Checkout Response`, response);
}

const resetScanner = () => {
  scanner_options.value.status = 0;
  scanner_options.value.code = null;
  scanner_options.message = null;
}

onMounted(() => {
  // Mount the scanner here...
});
</script>
<template>
  <AppLayout title="Scan Tickets">
    <template #header>
      <h1>Scan Tickets</h1>
    </template>

    <v-container>
      <v-row justify="center">
        <h1>
          <v-switch color="primary" v-model="scanner_options.mode"
                    true-value="checkin" false-value="checkout"
                    false-icon="mdi-exit" density="comfortable"
                    :label="scanner_options.mode === 'checkin' ? `Checking In` : `Checking Out`"/>
        </h1>

      </v-row>
      <v-row justify="center">
        <v-col cols="12" md="12" lg="4">
          <v-card>
            <v-card-title>
              Scan Tickets
              <v-chip label color="primary">{{ event.name }}</v-chip>
            </v-card-title>
            <v-card-text v-if="!scanner_options.status">
              <qrcode-stream @decode="doDecode"></qrcode-stream>
            </v-card-text>
            <v-card-text v-else class="text-center">
              <template v-if="scanner_options.status === 2">
                <v-alert color="success" type="success">
                  <v-alert-title>SUCCESS: {{scanner_options.message}}</v-alert-title>
                </v-alert>
              </template>
              <template v-else-if="scanner_options.status === 3">
                <v-alert color="error" type="error">
                  <v-alert-title>ERROR: {{scanner_options.message}}</v-alert-title>
                </v-alert>
              </template>
              <template v-else>
                <h3>{{ scanner_options.message }}</h3>
                <v-progress-circular width="24" color="primary" size="128"
                                     indeterminate class="my-8"/>
              </template>

              <div class="d-block my-8">
                <v-btn color="primary" @click="resetScanner"
                       :loading="scanner_options.status === 1">Scan Next
                </v-btn>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </AppLayout>
</template>
