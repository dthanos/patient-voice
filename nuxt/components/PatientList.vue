<template>
    <v-data-table-server
        class="px-5 pt-5"
        v-on="datatableStore.on"
        v-bind="options"
        :loading="datatableStore.loading"
        :headers="headers"
        :items="items"
    >
        <template v-for="headerItem in headers" v-slot:[`header.${headerItem.value}`]>
            <div v-if="headerItem.hasOwnProperty('variant')">
                <v-icon
                    size="large"
                    icon="mdi-plus-circle-outline"
                    color="success"
                    @click="onPatientCreate"
                />
            </div>
            <div
                v-else
                :class="`${headerItem.sortable ? 'cursor-pointer' : ''} font-weight-bold`"
                style="width: 150px !important"
            >
                <div>
                    {{ headerItem.title }}
                    <v-icon
                        v-if="sort?.key === headerItem.value"
                        class="pl-1"
                        size="small"
                        :icon="`mdi-arrow-${sort?.order === 'desc' ? 'down' : 'up'}`"
                    />
                </div>
                <v-text-field
                    clearable
                    variant="outlined"
                    density="compact"
                    class="pt-1"
                    v-if="headerItem.value !== 'age'"
                    :model-value="datatableStore.filters[headerItem.value] ?? ''"
                    @click.stop="onHeaderClicked"
                    @update:model-value="e => datatableStore.filters[headerItem.value] = e"
                />
                <v-select
                    clearable
                    v-else
                    variant="outlined"
                    density="compact"
                    class="pt-1"
                    :model-value="datatableStore.filters[headerItem.value] ?? ''"
                    :items="ageRanges"
                    @click.stop="onHeaderClicked"
                    @update:model-value="e => datatableStore.filters[headerItem.value] = e"
                />
            </div>
        </template>
        <template v-slot:item="{ item, index }">
            <tr class="cursor-pointer hover" @click="onPatientEdit(item)">
                <td>{{ item.name }}</td>
                <td>{{ item.email }}</td>
                <td>{{ item.gender }}</td>
                <td>{{ item.age }}</td>
                <td>{{ item.disease }}</td>
                <td>{{ item.medication }}</td>
            </tr>
        </template>
        <template v-slot:loading>
            <v-skeleton-loader type="table-tbody" />
        </template>
    </v-data-table-server>
</template>


<script setup>
import {computed, toRaw, watch} from "vue";
import {useDatatableStore} from "~/components/stores/datatable";
import {storeToRefs} from "pinia";
import {datetimeDatabaseToHuman} from "~/util/helpers.js";
import {useRouter} from "vue-router";
import {usePatientStore} from "~/components/stores/patient";
const datatableStore = useDatatableStore();
const { api, items, options, headers, meta, search, sort } = storeToRefs(datatableStore);
const patientStore = usePatientStore();
const { loading, modal, modalTitle, state, patient } = storeToRefs(patientStore);
const {push} = useRouter()
const router = useRouter()
const ageRanges = [
    { value: "0-1", title: "0 - 1 (Infant / Neonatal)" },
    { value: "1-4", title: "1 - 4 (Toddler)" },
    { value: "5-12", title: "5 - 12 (Child)" },
    { value: "13-17", title: "13 - 17 (Adolescent)" },
    { value: "18-24 ", title: "18 - 24 (Young Adult)" },
    { value: "25-44", title: "25 - 44 (Adult)" },
    { value: "45-64", title: "45 - 64 (Middle-aged Adult)" },
    { value: "65-120", title: "65+ (Older Adult / Senior)" },
]
api.value = { index: `/api/patient` }
headers.value = [
    {title: 'Name', value: 'name', sortable: true},
    {title: 'Email', value: 'email', sortable: true},
    {title: 'Gender', value: 'gender', sortable: true},
    {title: 'Age', value: 'age', sortable: true},
    {title: 'Disease', value: 'disease', sortable: true},
    {title: 'Medication', value: 'medication', sortable: true},
    {title: 'Create', value: 'create', sortable: false, variant: 'action'},
]

const onPatientCreate = () => {
    patient.value = {};
    state.value = true;
    modalTitle.value = 'Create Patient';
    modal.value = true;
}
const onPatientEdit = (item) => {
    patient.value = structuredClone(toRaw(item));
    state.value = false;
    modalTitle.value = 'Edit Patient';
    modal.value = true;
}
/*const onHeaderClicked = (e, item) => {
    debugger;
    e.stopPropagation();
    e.preventDefault();
    if (!item.sortable) return;

    const hits = sort.value.key === item.value ? (sort.value.order === 'asc' ? 2 : 3) : 1;
    sort.value = {
        key: item.value,
        order:  hits === 2 ? 'desc' : 'asc'
    };
}*/
const onHeaderClicked = (e) => {}
onUnmounted(() => datatableStore.filters = {})
</script>
<style scoped>
.hover:hover {
    background-color: rgba(0, 0, 255, 0.1); /* Light blue */
}
</style>