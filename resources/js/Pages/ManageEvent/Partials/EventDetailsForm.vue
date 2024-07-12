<script setup>
import {useForm, usePage} from '@inertiajs/vue3';
import {computed, ref} from "vue";

const props = defineProps({
  event: Object
});

const page = usePage();

const createEventForm = useForm(props.event);

const pickers = ref({
  event_date: false,
  event_start: false,
  event_end: false
});

const createEvent = () => {
  if (createEventForm.uuid) {
    // We are editing an existing form here!
    createEventForm.patch(route('manage-event.update', props.event.uuid), {
      preserveScroll: true,
      errorBag: 'eventDetails'
    });
  } else {
    createEventForm.post(route('manage-event.store'), {
      preserveScroll: true,
      errorBag: 'eventDetails'
    });
  }
}

const rules = {
  required: (v) => !!v || 'Field is required'
}

const button_label = computed(() => {
  if (createEventForm.uuid) {
    return 'Update';
  }
  return 'Create';
})

const cancel_route = computed(() => {
  if (createEventForm.uuid) {
    return route('manage-event.show', createEventForm.uuid);
  } else {
    return route('manage-event.index');
  }
})

const event_date = computed(() => {
  if (createEventForm.event_date === null) {
    return null;
  }
  if (typeof createEventForm.event_date === "string") {
    createEventForm.event_date = new Date(createEventForm.event_date);
  }
  return createEventForm.event_date.toLocaleDateString();
});
</script>
<template>
  <v-form @submit.prevent="createEvent">
    <v-card elevation="0">
      <v-card-text v-if="$page.props.errors.eventDetails">
        <v-alert type="error" class="mb-4" closeable>
          <v-alert-title>ERROR</v-alert-title>
          <p v-for="(error,key) in $page.props.errors.eventDetails">
            {{ error }}
          </p>
        </v-alert>
      </v-card-text>
      <v-card-text>
        <v-text-field v-model="createEventForm.name"
                      :rules="[rules.required]" label="Event Name"/>
        <v-row>
          <v-col cols="12" md="6">
            <p>
              <label>How many minutes should a user have to return
                a signed payload?
              </label>
            </p>
            <v-number-input
              v-model="createEventForm.nonce_valid_for_minutes"
              label="" control-variant="split" :min="1"
              :step="1"/>
          </v-col>
          <v-col cols="12" md="6">
            <p>
              <label>Must user continue to hold the asset(s) at
                the time of check-in?
              </label>
            </p>
            <v-btn-toggle v-model="createEventForm.hodl_asset"
                          mandatory color="primary" divided
                          class="mb-4">
              <v-btn :value="true">Yes</v-btn>
              <v-btn :value="false">No</v-btn>
            </v-btn-toggle>
          </v-col>
        </v-row>
        <v-label>Ticket Generation Times</v-label>
        <p class="text-caption">
          Times specified here only apply to when users can connect their
          wallet, verify their asset ownership, and generate tickets to the event.
        </p>
        <v-row>
          <v-col cols="12" md="6">
            <v-text-field type="datetime-local"
                          v-model="createEventForm.start_date_time"
                          label="Ticketing Starts"/>
          </v-col>
          <v-col cols="12" md="6">
            <v-text-field type="datetime-local"
                          v-model="createEventForm.end_date_time"
                          label="Ticketing Ends"/>
          </v-col>
        </v-row>
        <v-text-field v-model="createEventForm.location"
                      label="Where is the event being held?"/>
        <v-text-field v-model="event_date"
                      label="When is the event being held?" readonly
                      :active="pickers.event_date"
                      :focus="pickers.event_date">
          <v-menu v-model="pickers.event_date"
                  :close-on-content-click="false" activator="parent"
                  transition="scale-transition">
            <v-date-picker color="primary"
                           v-model="createEventForm.event_date"
                           show-adjacent-months></v-date-picker>
          </v-menu>
        </v-text-field>
        <v-row>
          <v-col cols="12" md="6">
            <v-text-field v-model="createEventForm.event_start"
                          label="Event Start Time"/>
          </v-col>
          <v-col cols="12" md="6">
            <v-text-field v-model="createEventForm.event_end"
                          label="Event End Time"/>
          </v-col>
        </v-row>
      </v-card-text>
      <v-card-actions>
        <v-btn color="primary" type="submit">{{ button_label }} Event
        </v-btn>
        <v-btn color="danger" type="cancel" :href="cancel_route">
          Cancel
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-form>
</template>
