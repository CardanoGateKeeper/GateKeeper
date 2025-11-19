<script setup>

</script>
<template>
  <v-system-bar window
    v-if="$page.props.environment?.is_preprod"
    color="warning"
    density="compact"
    class="align-center">
    <v-spacer/>
    <p>This is a demo / pre-production instance of GateKeeper.
      Any data you enter here may be reset automatically at
      &nbsp;<b>00:00 UTC</b> &nbsp;each day.</p>
    <v-spacer/>
  </v-system-bar>
</template>
