<template>
    <v-form ref="form" v-if="patient !== undefined">
        <v-label>Basic Info</v-label>
        <v-divider class="mb-5" />
        <v-row>
            <v-col md="12" class="pt-0 pb-1">
                <v-text-field
                    :rules="[value => !!value || 'Field is required']"
                    v-model="patient.name"
                    label="Name"
                    append-inner-icon="mdi-account"
                />
            </v-col>
            <v-col md="12" class="pt-0 pb-1">
                <v-text-field
                    :rules="[value => !!value || 'Field is required']"
                    v-model="patient.email"
                    label="Email"
                    append-inner-icon="mdi-at"
                />
            </v-col>
        </v-row>
        <v-row>
            <v-col sm="12" md="6" class="pt-0 pb-1">
                <v-text-field
                    :rules="[]"
                    v-model="patient.gender"
                    label="Gender"
                    append-inner-icon="mdi-gender-male-female-variant"
                />
            </v-col>
            <v-col sm="12" md="6" class="pt-0 pb-1">
                <v-text-field
                    :rules="[value => !!value || 'Field is required']"
                    v-model="patient.age"
                    label="Age"
                    append-inner-icon="mdi-calendar-range"
                />
            </v-col>
        </v-row>
        <v-spacer class="mt-5" />
        <v-label>Medical Info</v-label>
        <v-divider class="mb-5" />
        <v-row>
            <v-col md="12" class="pt-0 pb-1">
                <v-text-field
                    :rules="[]"
                    v-model="patient.disease"
                    label="Disease"
                    append-inner-icon="mdi-biohazard"
                />
            </v-col>
            <v-col md="12" class="pt-0 pb-1">
                <v-text-field
                    :rules="[]"
                    v-model="patient.medication"
                    label="Medication"
                    append-inner-icon="mdi-medical-bag"
                />
            </v-col>
        </v-row>
    </v-form>
</template>


<script setup>
import {storeToRefs} from "pinia";
import {usePatientStore} from "~/components/stores/patient";
const patientStore = usePatientStore();
const { patient, loading, form, state } = storeToRefs(patientStore);
</script>
