import type {Patient} from "~~/util/types";
import {useSnackbarStore} from "~/components/stores/snackbar";
import {apiErrorHandler} from "~~/util/helpers";

export async function indexPatient() {
    return fetch(`/api/patient`).then((r: any) => r.data.data)
}
export async function showMe() {
    return fetch(`/api/patient/ip`).then((r: any) => r?.data?.data ?? null)
}

export async function updatePatient(patient: Patient) {
    const snackbarStore = useSnackbarStore()
    return fetch(`/api/patient?id=${patient.id}`, {
        method: 'put',
        body: JSON.stringify(patient)
    })
        .then(async (r: any) => {
            snackbarStore.addSnackbar({text: 'Patient updated successfully.'});
            const res = await r.json();
            return res.data;
        })
        .catch((r: any) => apiErrorHandler(r.response.data.errors))
}

export async function storePatient(patient: Patient) {
    const snackbarStore = useSnackbarStore()
    return fetch(`/api/patient`, {
        method: 'post',
        body: JSON.stringify(patient)
    })
        .then(async (r: any) => {
            snackbarStore.addSnackbar({text: 'Patient created successfully.'});
            const res = await r.json();
            return res.data;
        })
        .catch((r: any) => apiErrorHandler(r.response.data.errors))
}

export async function destroyPatient(patient: Patient) {
    const snackbarStore = useSnackbarStore()
    return fetch(`/api/patient?id=${patient.id}`, {
        method: 'delete',
        body: JSON.stringify(patient)
    })
        .then(async (r: any) => {
            snackbarStore.addSnackbar({text: 'Patient deleted successfully.'});
            const res = await r.json();
            return res.data;
        })
        .catch((r: any) => apiErrorHandler(r.response.data.errors))
}
