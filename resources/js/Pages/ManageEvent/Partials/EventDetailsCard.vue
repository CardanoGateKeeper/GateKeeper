<script setup>
const props = defineProps({
  event: Object
});

const bg_image = 'url(' + props.event.bg_image_url + ')';
</script>
<style>
.v-card-title#event-header {
  color: white;
  background: linear-gradient(to right, rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0.3)),
  v-bind('bg_image'),
  linear-gradient(rgba(114, 76, 195, 1.0),
    rgba(114, 76, 195, 1.0)) center center;
  background-size: cover;

}
</style>
<template>
  <v-card>
    <v-card-title class="d-flex justify-center py-8" id="event-header">
      <v-avatar :image="event.profile_photo_url" class="me-4" size="96"/>
      <div class="text-start">
        <p>
          <v-avatar :image="event.team.profile_photo_url" size="24"
                    class="me-2"/>
          {{ event.team.name }} presents
        </p>
        <h2>{{ event.name }}</h2>
      </div>

      <!--            <v-avatar :image="event_pfp" size="25%" class="mb-4"/>
                  <v-card-subtitle>{{ event.team.name }} Presents</v-card-subtitle>
                  <h3 style="white-space: normal">{{ event.name }}</h3>-->
    </v-card-title>
    <hr/>
    <v-card-text v-if="event.description">
      {{ event.description }}
    </v-card-text>
    <v-card-text class="font-weight-bold">
      <p v-if="event.location">
        <v-icon icon="mdi-web" class="me-2"/>
        {{ event.location }}
      </p>
      <p>
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
    </v-card-text>
    <v-card-text>
      Ticket generation available from {{ event.start_date_time }} UTC
      until {{ event.end_date_time }} UTC
    </v-card-text>
    <v-card-text>
      Event created/owned by {{ event.user.name }}
    </v-card-text>
    <v-card-text>
      <v-row>
        <v-col cols="12" md="6">
          <v-card color="primary" class="text-center">
            <v-card-title>
              <h5>{{ event.policies.length }}</h5>
            </v-card-title>
            <v-card-subtitle>Eligible Policies</v-card-subtitle>
          </v-card>
        </v-col>
        <v-col cols="12" md="6">
          <v-card color="secondary" class="text-center">
            <v-card-title>
              <h5>{{ event.tickets_count }}</h5>
            </v-card-title>
            <v-card-subtitle>Generated Tickets</v-card-subtitle>
          </v-card>
        </v-col>
      </v-row>
    </v-card-text>
    <!--        <v-card-text>
                <pre>{{ event }}</pre>
            </v-card-text>-->
    <v-card-actions>
      <v-btn color="primary"
             :href="route('manage-event.edit',event.uuid)">
        <v-icon icon="mdi-pencil"></v-icon>
        Edit
      </v-btn>
      <v-spacer/>
      <v-btn color="secondary" :href="route('event.show', event.uuid)"
             target="_blank">
        <v-icon icon="mdi-external-link"></v-icon>
        View Public Page
      </v-btn>
    </v-card-actions>
  </v-card>
</template>
