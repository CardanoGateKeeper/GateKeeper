<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import EventDetailsCard
  from "@/Pages/ManageEvent/Partials/EventDetailsCard.vue";
import {Bar, Doughnut, Line} from 'vue-chartjs';
import {computed} from "vue";
import {
  Chart as ChartJS,
  ArcElement,
  Colors,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
  RadialLinearScale,
  TimeScale,
  PointElement,
  LineElement
} from 'chart.js';
import 'chartjs-adapter-moment';

ChartJS.register(
  Colors, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale,
  RadialLinearScale, ArcElement, TimeScale, PointElement, LineElement);

const attendance_by_time_options = {
  responsive: true,
  scales:     {
    x: {
      type: "time",
      time: {
        unit: "minute"
      }
    },
    y: {
      ticks: {
        stepSize: 1
      }
    }
  }
}

const props = defineProps({
  event: Object,
})

const chartData = computed(() => {
  return {
    datasets: [
      {
        label: 'Tickets Created',
        data:  props.event.stats.tickets.byDate
      }
    ]
  };
})

const chartOptions = {
  responsive: true,
  scales:     {
    x: {
      type: "time",
      time: {
        unit: "day"
      }
    },
    y: {
      ticks: {
        stepSize: 1
      }
    }
  }
};

const policyOptions = {
  responsive: true
}
</script>
<template>
  <AppLayout title="Manage Event">
    <v-container>
      <v-row>
        <v-col cols="12" lg="6" xl="4">
          <h1 class="text-center mb-8">Manage Event</h1>

          <EventDetailsCard :event="event"/>
        </v-col>
        <v-col cols="12" lg="6" xl="8">
          <v-row class="text-center">
            <v-col cols="12">
              <h2>Ticket Stats</h2>
            </v-col>
            <v-col cols="12" md="6">
              <v-card>
                <v-card-title>Ticket Generation by Date</v-card-title>
                <v-card-text>
                  <Bar id="tickets-generated" :data="chartData"
                       :options="chartOptions"/>
                </v-card-text>
              </v-card>
            </v-col>
            <v-col cols="12" md="6">
              <v-card>
                <v-card-title>Ticket Generation by Policy</v-card-title>
                <v-card-text>
                  <div class="d-flex flex-row justify-center"
                       style="max-height: 436px">
                    <Doughnut id="ticketed-policies"
                              :data="event.stats.tickets.policy.pieChart"
                              :options="policyOptions"/>
                  </div>
                </v-card-text>
              </v-card>
            </v-col>
            <v-col cols="12">
              <h2>Attendance Stats</h2>
            </v-col>
            <v-col cols="12" md="6">
              <v-row>
                <v-col cols="6">
                  <v-card>
                    <v-card-title>Unique Tickets Checked In</v-card-title>
                    <v-card-text class="text-h1">{{
                        event.stats.attendance.unique_checkin
                      }}
                    </v-card-text>
                  </v-card>
                </v-col>
                <v-col cols="6">
                  <v-card>
                    <v-card-title>Unique Tickets Checked Out</v-card-title>
                    <v-card-text class="text-h1">{{
                        event.stats.attendance.unique_checkout
                      }}
                    </v-card-text>
                  </v-card>
                </v-col>
                <v-col cols="12">
                  <v-card>
                    <v-card-title>Attendance Over Time</v-card-title>
                    <v-card-text style="max-height:436px" class="text-center">
                      <Line :data="event.stats.attendance.byTime"
                            :options="attendance_by_time_options"/>
                    </v-card-text>
                  </v-card>
                </v-col>
              </v-row>
            </v-col>
            <v-col cols="12" md="6">
              <v-card>
                <v-card-title>Staff Performance</v-card-title>
                <v-table>
                  <thead>
                  <tr>
                    <th>Staff</th>
                    <th>Checkin</th>
                    <th>Checkout</th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr v-for="(stats,user) in event.stats.attendance.byUser"
                      :key="user" class="text-start">
                    <td>{{ user }}</td>
                    <td>{{ stats.checkin }}</td>
                    <td>{{ stats.checkout }}</td>
                  </tr>
                  </tbody>
                </v-table>
              </v-card>
            </v-col>

          </v-row>
        </v-col>
      </v-row>
    </v-container>
  </AppLayout>
</template>
