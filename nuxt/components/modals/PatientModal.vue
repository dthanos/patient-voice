<template>
    <v-dialog v-model="modal" transition="scale-transition" class="dimensions" min-width="700px" @click:outside="onDismiss">
        <v-card :loading="loading">
            <v-card-title class="d-flex align-items-center justify-space-between border-b-sm">
                <h3 class="ml-2"><small>{{ modalTitle }}</small></h3>
                <v-btn class="text-right" elevation="0" light @click="onDismiss">
                    <v-icon icon="mdi-close"></v-icon>
                </v-btn>
            </v-card-title>
            <v-divider></v-divider>
            <v-card-text class="pa-5">
                <p v-if="modalSubTitle" class="pb-6 font-italic">{{modalSubTitle}}</p>
                <PatientForm />
            </v-card-text>
            <v-card-actions>
                <v-btn
                    v-if="!state"
                    variant="tonal"
                    color="error"
                    text="Delete"
                    @click="onDelete"
                />
                <v-spacer></v-spacer>
                <v-btn
                    color="error"
                    text="Dismiss"
                    @click="onDismiss"
                />
                <v-btn
                    color="success"
                    :text="state ? 'Create' : 'Save'"
                    @click="onConfirm"
                />
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script setup>
import {storeToRefs} from "pinia";
import {usePatientStore} from "~/components/stores/patient";
import PatientForm from "~/components/PatientForm.vue";
import {useSnackbarStore} from "~/components/stores/snackbar";
const snackbarStore = useSnackbarStore()
const patientStore = usePatientStore()
const {modal, modalTitle, modalSubTitle, state, patient, form, loading} = storeToRefs(patientStore)
const emits = defineEmits(['confirmed', 'cancelled'])

async function onConfirm() {
    const validation = await form.value.validate()
    if (!validation.valid) {
        snackbarStore.addSnackbar({ variant: 'red', text: 'Please fill all required fields.' })
        return;
    }

    state.value ? await patientStore.createPatient() : await patientStore.editPatient();
    patientStore.fetchData();
    modal.value = false;
    patient.value = {};

    emits('confirmed', true);
}
async function onDelete() {
    await patientStore.deletePatient();
    patientStore.fetchData();
    modal.value = false;
    patient.value = {};

    emits('confirmed', true);
}
function onDismiss(){
    modal.value = false;
    patient.value = {};

    emits('cancelled', true);
}
</script>
<style>
.dimensions {
    width: 500px;
    height: 650px;
}
</style>
