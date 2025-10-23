<script setup>
import {computed, ref} from "vue";
import {router, useForm, usePage} from "@inertiajs/vue3";

const props = defineProps({
    event: Array,
    team_policies: Array,
});

// const team = ref(props.event.team);
// const event_policies = ref(props.event.policies);

const page = usePage();

const demo_policies = [
    {
        name: 'Claymates',
        hash: '1f50d35118eee21dbf21575a667a4a8f4596ff9289cc3319ad9ecd4f'
    },
    {
        name: 'The Ape Society: OG',
        hash: 'e573c5ec32b0f66ad4593f411142a3ca7bb0304fae69d10a17e15c8b'
    }
];

const new_team_policy_form = useForm({
    name: null,
    hash: null
});

const modals = ref({
    add_team_policy: false,
    create_team_policy: false
});

const addPolicy = (policy) => {
    // console.log("Attempting to add policy?", props.event, policy);
    router.post(route('event.policy.store', props.event.uuid), {
        policy_id: policy.id
    }, {
        onSuccess: () => {
            modals.add_team_policy = false;
            router.reload({only: ['event']});
        }
    });
}

const removePolicy = (policy) => {
    router.delete(route('event.policy.destroy', {
        event: props.event.uuid,
        policy
    }), {
        policy_id: policy.id
    }, {
        onSuccess: () => {
            router.reload({only: ['event']})
        }
    })
}

const policyAssigned = function (policy) {
    policy = policy || {hash: 'abc123'};
    let found = false;
    props.event.policies.forEach((assigned) => {
        if (found) return;
        if (assigned.hash === policy.hash) {
            found = true;
        }
    });
    return found;
}

const createPolicy = () => {
    new_team_policy_form.post(route('team.policy.store', props.event.team), {
        errorBag: 'teamPolicy',
        onSuccess: () => {
            new_team_policy_form.reset();
            modals.create_team_policy = false;
        }
    });
}

const unassignedPolicies = computed(() => {
    const policies = [];
    props.team_policies.forEach((policy) => {
        let found = false;
        props.event.policies.forEach((assigned) => {
            if (found) {
                return;
            }
            if (assigned.hash === policy.hash) {
                found = true;
            }
        });
        if (!found) {
            policies.push(policy);
        }
    })
    return policies;
});

const rules = {
    required: (v) => !!v || 'Field is required',
    hex: (v) => {
        return /^[a-f0-9]{56}$/i.test(v) || 'Invalid policy ID!';
    }
}
</script>
<template>
    <v-row justify="end" color="transparent" class="mb-4">
        <v-btn color="primary" @click="modals.add_team_policy = true">Add Policy
        </v-btn>
    </v-row>
    <v-table>
        <thead>
        <tr>
            <th>Name</th>
            <th>Hash</th>
            <th>&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <tr v-if="event.policies.length === 0">
            <td colspan="3">No policies have been assigned yet!</td>
        </tr>
        <tr v-else v-for="policy in event.policies" :key="policy.hash">
            <td>{{ policy.name }}</td>
            <td>{{ policy.hash }}</td>
            <td class="text-end">
                <v-btn @click="removePolicy(policy)">Remove</v-btn>
            </td>
        </tr>
        </tbody>
    </v-table>
    <v-dialog v-model="modals.add_team_policy" persistent width="auto"
              scrollable>
        <v-card title="Add Eligible Policies to This Event">
            <v-card-text>
                <v-list lines="two">
                    <v-list-subheader>Choose a Policy</v-list-subheader>
                    <v-list-item v-for="policy in unassignedPolicies"
                                 :key="policy.hash" :title="policy.name"
                                 :subtitle="policy.hash">
                        <template v-slot:prepend>
                            <v-avatar
                                :image="policy.profile_photo_url"></v-avatar>
                        </template>
                        <template v-slot:append>
                            <v-btn color="primary" variant="text"
                                   @click="addPolicy(policy)">ADD
                            </v-btn>
                        </template>
                    </v-list-item>
                </v-list>
                <!--                <pre>{{ team_policies }}</pre>-->
            </v-card-text>
            <v-card-actions>
                <v-btn @click="modals.add_team_policy = false">Done</v-btn>
                <v-btn color="primary"
                       @click="modals.create_team_policy = true">Add New
                </v-btn>
                <v-spacer></v-spacer>
            </v-card-actions>
        </v-card>
    </v-dialog>
    <v-dialog v-model="modals.create_team_policy" persistent min-width="560px"
              width="auto">
        <v-card title="Add a New Team Policy">
            <v-form @submit.prevent="createPolicy">
                <v-card-text v-if="$page.props.errors.teamPolicy">
                    <v-alert type="error" class="mb-4">
                        <v-alert-title>ERROR</v-alert-title>
                        <p v-for="(error,key) in $page.props.errors.teamPolicy">
                            {{ error }}
                        </p>
                    </v-alert>
                </v-card-text>
                <v-card-text>
                    <v-text-field :rules="[rules.required]" label="Name"
                                  v-model="new_team_policy_form.name"/>
                    <v-text-field :rules="[rules.required, rules.hex]"
                                  label="Policy ID" minlength="56"
                                  maxlength="56" counter
                                  v-model="new_team_policy_form.hash"/>
                </v-card-text>
                <v-card-actions>
                    <v-btn type="submit" color="primary">Create</v-btn>
                    <v-btn type="button"
                           @click="modals.create_team_policy = false">Done
                    </v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
    </v-dialog>
</template>
