import {defineStore} from "pinia";
import {ref} from "vue";
import type {Ref} from "vue";
import {useRoute, useRouter} from "vue-router";
import type {Patient} from "~/util/types";
import {showMe, updatePatient, storePatient, destroyPatient} from "~/services/patient";
import {useDatatableStore} from "~/components/stores/datatable";

export const usePatientStore = defineStore('patient', () => {
    const {push, back} = useRouter()
    const patient: Ref<Patient> = ref({})
    const form: Ref<any> = ref(null)
    const loading = ref(false)
    const modal = ref(false)
    const state = ref(true)
    const modalTitle = ref('')
    const modalSubTitle = ref('')
    const route = useRoute()
    const datatableStore = useDatatableStore();

    fetchData();

    async function fetchData(){
        loading.value = true;
        datatableStore.api.index = `/api/patient`;
        datatableStore.meta = { page: Number(route.query.page) || 1, itemsPerPage: Number(route.query.itemsPerPage) || 25 };
        loading.value = false;
    }
    async function fetchMe(){
        loading.value = true;
        await showMe().then(r => patient.value = r).finally(() => loading.value = false)
    }
    async function editPatient(){
        loading.value = true;
        await updatePatient(patient.value).then(r => patient.value = r).finally(() => loading.value = false)
    }
    async function createPatient(){
        loading.value = true;
        await storePatient(patient.value).then(r => patient.value = r).finally(() => loading.value = false)
    }
    async function deletePatient(){
        loading.value = true;
        await destroyPatient(patient.value).finally(() => loading.value = false)
    }
    return {
        patient,
        loading,
        form,
        modal,
        modalTitle,
        modalSubTitle,
        state,

        fetchData,
        fetchMe,
        editPatient,
        createPatient,
        deletePatient,
    }
})
