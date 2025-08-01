<template>
    <PatientList />
    <PatientModal />
</template>

<script setup>
import {useGlobalStore} from "~/components/stores/global.js";
import {storeToRefs} from "pinia";
import PatientList from "~/components/PatientList.vue";
import {onMounted} from "vue";
import PatientModal from "~/components/modals/PatientModal.vue";
const globalStore = useGlobalStore();
const { breadcrumbsTree, breadcrumbs, breadcrumbsLoading } = storeToRefs(globalStore);

onMounted(() => {
    breadcrumbs.value = true;
    breadcrumbsTree.value = [
        {title: 'Patients', disabled: true},
    ]
    breadcrumbsLoading.value = false;
})
</script>
