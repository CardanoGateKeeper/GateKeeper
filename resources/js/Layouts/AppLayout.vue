<script setup>
import {onMounted, ref} from 'vue';
import {Head, Link, router} from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import {useTheme} from "vuetify";
import Banner from "@/Components/Banner.vue";
import NetworkNotice from "@/Components/NetworkNotice.vue";

defineProps({
  title: String,
});

const theme = useTheme()

function toggleTheme() {
  const theme_value = theme.global.current.value.dark ? 'light' : 'dark'
  localStorage.setItem('gatekeeper:theme', theme_value);
  theme.global.name.value = theme_value;
}

const localTheme = ref({
  value: null
});

onMounted(() => {
  localTheme.value = localStorage.getItem('gatekeeper:theme') ?? 'light';
  theme.global.name.value = localTheme.value;
})

const showingNavigationDropdown = ref(false);
const showingNavigation = ref(false);

const switchToTeam = (team) => {
  router.put(route('current-team.update'), {
    team_id: team.id,
  }, {
    preserveState: false,
  });
};

const logout = () => {
  router.post(route('logout'));
};
</script>
<style>
.v-toolbar-title .gatekeeper {
  height: 48px;
  max-height: 48px;
  width: auto;
  margin-right: 0.5em;
  fill: rgba(var(--v-theme-on-surface), var(--v-high-emphasis-opacity))
}

header h1 {
  font-family: Lexend, 'Open Sans', sans-serif;
  font-size: 34px;
}

h1, h2, h3, h4, h5 {
  font-family: Lexend, 'Open Sans', sans-serif;
}

footer a {
  color: rgb(var(--v-theme-on-primary)) !important;;
}
</style>
<template>
  <Head :title="title"/>
  <v-app>
    <NetworkNotice />

    <v-main>
      <v-toolbar class="d-flex flex-row" color="transparent">
        <v-app-bar-nav-icon
          @click.stop="showingNavigation = !showingNavigation"></v-app-bar-nav-icon>
        <v-toolbar-title class="flex-0-1">
          <Link :href="route('dashboard')">
            <ApplicationMark/>
          </Link>
        </v-toolbar-title>
        <v-toolbar-items class="flex-fill">
          <v-btn :href="route('dashboard')"
                 :active="route().current('dashboard')">Dashboard
          </v-btn>
          <v-btn :href="route('manage-event.index')"
                 :active="$page.component.startsWith('ManageEvent')">Events
          </v-btn>
        </v-toolbar-items>
        <v-spacer></v-spacer>
        <v-toolbar-items>
          <v-btn @click="toggleTheme">
            <v-icon
              :icon="theme.global.current.value.dark ? 'mdi-weather-sunny' : 'mdi-weather-night'"/>
          </v-btn>
          <v-menu v-if="$page.props.jetstream.hasTeamFeatures">
            <template v-slot:activator="{ props }">
              <v-btn color="secondary" v-bind="props"
                     :active="route().current('teams.show', $page.props.auth.user.current_team)">
                <template v-slot:prepend>
                  <v-avatar>
                    <v-img
                      :src="$page.props.auth.user.current_team.profile_photo_url"/>
                  </v-avatar>
                </template>
                {{ $page.props.auth.user.current_team.name }}
              </v-btn>
            </template>
            <v-list>
              <v-list-subheader>Manage Team</v-list-subheader>
              <v-list-item
                :href="route('teams.show', $page.props.auth.user.current_team)">
                <v-list-item-title>Team Settings</v-list-item-title>
              </v-list-item>
              <v-list-item v-if="$page.props.jetstream.canCreateTeams"
                           :href="route('teams.create')">
                <v-list-item-title>Create New Team
                </v-list-item-title>
              </v-list-item>
              <template
                v-if="$page.props.auth.user.all_teams.length > 1">
                <v-divider></v-divider>
                <v-list-subheader>Switch Team</v-list-subheader>
                <v-list-item
                  v-for="team in $page.props.auth.user.all_teams"
                  :key="team.id" @click="switchToTeam(team)">
                  <template v-slot:append
                            v-if="team.id == $page.props.auth.user.current_team_id">
                    <v-icon icon="mdi-check-circle-outline"/>
                  </template>
                  <template v-slot:prepend>
                    <v-avatar>
                      <v-img :src="team.profile_photo_url"
                             :alt="team.name"/>
                    </v-avatar>
                  </template>
                  <v-list-item-title>{{
                      team.name
                    }}
                  </v-list-item-title>
                </v-list-item>
              </template>
            </v-list>
          </v-menu>
          <v-menu>
            <template v-slot:activator="{ props }">
              <v-btn color="primary" v-bind="props"
                     :active="route().current('profile.show') || route().current('api-tokens.index')">
                <template v-slot:prepend
                          v-if="$page.props.jetstream.managesProfilePhotos">
                  <v-avatar>
                    <v-img
                      :src="$page.props.auth.user.profile_photo_url"
                      :alt="$page.props.auth.user.name"/>
                  </v-avatar>
                </template>
                {{ $page.props.auth.user.name }}
              </v-btn>
            </template>
            <v-list>
              <v-list-subheader>Manage Account</v-list-subheader>
              <v-list-item :href="route('profile.show')"
                           :active="route().current('profile.show')">
                <template v-slot:prepend>
                  <v-icon icon="mdi-account"/>
                </template>
                <v-list-item-title>Profile</v-list-item-title>
              </v-list-item>
              <v-list-item :href="route('api-tokens.index')"
                           :active="route().current('api-tokens.index')"
                           v-if="$page.props.jetstream.hasApiFeatures">
                <template v-slot:prepend>
                  <v-icon icon="mdi-api"/>
                </template>
                <v-list-item-title>API Tokens</v-list-item-title>
              </v-list-item>
              <v-divider></v-divider>
              <v-list-item @click="logout">
                <template v-slot:prepend>
                  <v-icon icon="mdi-logout"/>
                </template>
                <v-list-item-title>Logout</v-list-item-title>
              </v-list-item>
            </v-list>
          </v-menu>
        </v-toolbar-items>
      </v-toolbar>
      <v-container fluid>
        <header class="py-16 text-center">
          <slot name="header"></slot>
        </header>
        <v-container>
          <Banner/>
        </v-container>
        <slot></slot>
      </v-container>
    </v-main>
    <v-footer color="primary" class="flex-0-1">
      <v-row justify="center">
        <v-col cols="12" class="text-center">
          <p>
            Powered by <strong>GateKeeper OSS</strong> (
            <a href="https://github.com/CardanoGateKeeper/Core"
               target="_blank">
              View on GitHub
              <v-icon icon="mdi-github"></v-icon>
            </a>
            )
          </p>
          <p>
            An open source project created by Adam K. Dean &amp; Latheesan
            Kanesamoorthy
            <br/>
            Maintained by the Cardano community with
            <v-icon icon="mdi-heart" class="" title="Love"></v-icon>
          </p>
        </v-col>
      </v-row>
    </v-footer>
    <v-navigation-drawer v-model="showingNavigation" temporary>
      <v-list-subheader>GateKeeper</v-list-subheader>
      <v-list-item :href="route('dashboard')"
                   :active="route().current('dashboard')">
        <template v-slot:prepend>
          <v-icon icon="mdi-home"/>
        </template>
        <v-list-item-title>Dashboard</v-list-item-title>
      </v-list-item>
      <v-list-item :href="route('manage-event.index')"
                   :active="route().current('manage-event.*')"
                   title="Events">
        <template v-slot:prepend>
          <v-icon icon="mdi-calendar"/>
        </template>
      </v-list-item>
      <template v-if="$page.props.jetstream.hasTeamFeatures">
        <v-divider></v-divider>
        <v-list-subheader>Manage Team</v-list-subheader>
        <v-list-item
          :href="route('teams.show', $page.props.auth.user.current_team)">
          <template v-slot:prepend>
            <v-icon icon="mdi-account-multiple"/>
          </template>
          <v-list-item-title>Team Settings</v-list-item-title>
        </v-list-item>
        <v-list-item v-if="$page.props.jetstream.canCreateTeams"
                     :href="route('teams.create')">
          <template v-slot:prepend>
            <v-icon icon="mdi-account-multiple-plus"/>
          </template>
          <v-list-item-title>Create New Team</v-list-item-title>
        </v-list-item>
        <template v-if="$page.props.auth.user.all_teams.length > 1">
          <v-divider></v-divider>
          <v-list-subheader>Switch Team</v-list-subheader>
          <v-list-item v-for="team in $page.props.auth.user.all_teams"
                       :key="team.id" @click="switchToTeam(team)">
            <template v-slot:prepend
                      v-if="team.id == $page.props.auth.user.current_team_id">
              <v-icon icon="mdi-check-circle-outline"/>
            </template>
            <v-list-item-title>{{ team.name }}</v-list-item-title>
          </v-list-item>
        </template>
      </template>
      <v-divider></v-divider>
      <v-list-subheader>Manage Account</v-list-subheader>
      <v-list-item :href="route('profile.show')">
        <template v-slot:prepend>
          <v-icon icon="mdi-account"/>
        </template>
        <v-list-item-title>Profile</v-list-item-title>
      </v-list-item>
      <v-list-item :href="route('api-tokens.index')"
                   v-if="$page.props.jetstream.hasApiFeatures">
        <template v-slot:prepend>
          <v-icon icon="mdi-api"/>
        </template>
        <v-list-item-title>API Tokens</v-list-item-title>
      </v-list-item>
      <v-list-item @click="logout">
        <template v-slot:prepend>
          <v-icon icon="mdi-logout"/>
        </template>
        <v-list-item-title>Logout</v-list-item-title>
      </v-list-item>
    </v-navigation-drawer>
  </v-app>
</template>
