<script setup>
import {Head} from '@inertiajs/vue3';
import AppHeader from "@/Components/AppHeader.vue";
import HowItWorksBg from "@/media/how_it_works.svg"
import {ref} from "vue";

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

const how_it_works_bg = ref('url(' + HowItWorksBg + ')');
</script>
<template>
    <Head title="Welcome"/>
    <v-app class="d-flex">
        <AppHeader :can-login="canLogin" :can-register="canRegister"></AppHeader>
        <v-main class="flex-fill">
            <v-parallax src="https://picsum.photos/2048/1080" height="300" class="hero">
                <v-container class="d-flex flex-column fill-height justify-center align-center text-white">
                    <h2>
                        Convert your Cardano NFTs into
                        <br/>
                        tickets to real-world events
                    </h2>
                </v-container>
            </v-parallax>
            <div id="how-it-works" class="py-16">
                <v-container class="d-flex flex-column fill-height justify-center align-center">
                    <h2 class="pb-16">How It Works</h2>
                    <v-row justify="center" align="stretch">
                        <v-col cols="8" md="4">
                            <v-card class="text-center fill-height">
                                <v-card-title>
                                    <v-btn color="#C5CAFF" icon size="x-large">
                                        <v-icon size="x-large" icon="mdi-wallet"></v-icon>
                                    </v-btn>
                                </v-card-title>
                                <v-card-title>Connect Wallet</v-card-title>
                                <v-card-text class="flex">
                                    And get access to the dashboard to see available events
                                </v-card-text>
                            </v-card>
                        </v-col>
                        <v-col cols="8" md="4">
                            <v-card class="text-center fill-height">
                                <v-card-title>
                                    <v-btn color="#C5CAFF" icon size="x-large">
                                        <v-icon size="x-large" icon="mdi-ticket"></v-icon>
                                    </v-btn>
                                </v-card-title>
                                <v-card-title>Generate Tickets</v-card-title>
                                <v-card-text class="flex-fill">
                                    Turn your eligible NFTs into QR code
                                </v-card-text>
                            </v-card>
                        </v-col>
                        <v-col cols="8" md="4">
                            <v-card class="text-center fill-height">
                                <v-card-title>
                                    <v-btn color="#C5CAFF" icon size="x-large">
                                        <v-icon size="x-large" icon="mdi-party-popper"></v-icon>
                                    </v-btn>
                                </v-card-title>
                                <v-card-title>Attend Event</v-card-title>
                                <v-card-text class="flex">
                                    Show QR code at the entrance and have fun!
                                </v-card-text>
                            </v-card>
                        </v-col>
                    </v-row>
                </v-container>
            </div>
            <div id="faqs" class="py-16">
                <v-container class="text-center">
                    <h2>Frequently Asked Questions</h2>
                    <v-expansion-panels class="my-16">
                        <v-expansion-panel>
                            <v-expansion-panel-title>
                                What happens if I lose my QR code?
                                <template v-slot:actions="{expanded}">
                                    <v-icon :icon="expanded ? 'mdi-close' : 'mdi-plus'"/>
                                </template>
                            </v-expansion-panel-title>
                            <v-expansion-panel-text class="text-start">
                                If you misplace your QR code it's not a problem! Simply return to this page and generate
                                a new Ticket QR for your NFT!
                            </v-expansion-panel-text>
                        </v-expansion-panel>
                        <v-expansion-panel>
                            <v-expansion-panel-title>
                                How many times can I use a QR code?
                                <template v-slot:actions="{expanded}">
                                    <v-icon :icon="expanded ? 'mdi-close' : 'mdi-plus'"/>
                                </template>
                            </v-expansion-panel-title>
                            <v-expansion-panel-text class="text-start"></v-expansion-panel-text>
                        </v-expansion-panel>
                        <v-expansion-panel>
                            <v-expansion-panel-title>
                                What if I transfer my NFTs?
                                <template v-slot:actions="{expanded}">
                                    <v-icon :icon="expanded ? 'mdi-close' : 'mdi-plus'"/>
                                </template>
                            </v-expansion-panel-title>
                            <v-expansion-panel-text class="text-start"></v-expansion-panel-text>
                        </v-expansion-panel>
                        <v-expansion-panel>
                            <v-expansion-panel-title>
                                Do I have to sign anything in my wallet to get access to the dashboard?
                                <template v-slot:actions="{expanded}">
                                    <v-icon :icon="expanded ? 'mdi-close' : 'mdi-plus'"/>
                                </template>
                            </v-expansion-panel-title>
                            <v-expansion-panel-text class="text-start"></v-expansion-panel-text>
                        </v-expansion-panel>
                    </v-expansion-panels>
                </v-container>
            </div>
        </v-main>
        <v-footer color="primary" class="flex-0-1">
            <v-row justify="center">
                <v-col cols="12" class="text-center">
                    <p>
                        Powered by <strong>GateKeeper OSS</strong> (
                        <a href="https://github.com/CardanoGateKeeper/Core" target="_blank">
                            View on GitHub
                            <v-icon icon="mdi-github"></v-icon>
                        </a>
                        )
                    </p>
                    <p>
                        An open source project created by Adam K. Dean &amp; Latheesan Kanesamoorthy
                        <br/>
                        Maintained by the Cardano community with
                        <v-icon icon="mdi-heart" class="" title="Love"></v-icon>
                    </p>
                </v-col>
            </v-row>
        </v-footer>
    </v-app>
</template>
<style>
.v-footer a {
    color: inherit !important
}

h1, h2, h3, h4, h5 {
    font-family: Lexend, 'Open Sans', sans-serif;
}

h2 {
    font-size: 34px;
}

.v-parallax.hero .v-responsive__content {
    background: linear-gradient(180deg, rgba(0, 0, 0, 0.75) 0%, rgba(0, 0, 0, 0) 100%);
}

#how-it-works {
    background: v-bind(how_it_works_bg) center center;
    background-size: cover;
    border-bottom: 1px solid rgba(238, 229, 255, 0.5);
    /*border-bottom: 1px solid rgba(var(--v-theme-primary), 0.25);*/
    border-top: 1px solid rgba(238, 229, 255, 0.5);
}

#how-it-works .v-card {
    border-bottom-color: rgb(var(--v-theme-primary));
    border-bottom-width: 4px;
}
</style>
