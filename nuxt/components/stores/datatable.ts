import {defineStore} from "pinia";
import {computed, ref} from "vue";
import type {Ref} from "vue";
import axios from "axios";
import { watchDebounced } from '@vueuse/core'
import type {DatatableApi} from "~/util/types";
import qs from 'qs';

export const useDatatableStore = defineStore('datatable', () => {
    const api: Ref<DatatableApi> = ref({})
    const items = ref([])
    const search = ref('')
    const total = ref(null)
    const from = ref(0)
    const to = ref(25)
    const headers = ref([])
    const filters = reactive({})
    const meta = ref({
        page: 1,
        itemsPerPage: 25,
        last_page: 1,
        total: 1
    })
    const sort = ref({order: '', key: ''})
    const loading = ref(false)
    const options = computed(() => {
        return {
            // total: total.value,
            itemsPerPage: meta.value.itemsPerPage,
            page: meta.value.page,
            itemsPerPageOptions: [
                {value: 25, title: '25'},
                {value: 50, title: '50'},
                {value: 100, title: '100'},
            ],
            pageText: `${from.value}-${to.value} of ${total.value}`,
            height: 'calc(100vh - 200px)'
        }
    })

    async function fetchData() {
        loading.value = true
        const queryString = qs.stringify({
            page: meta.value.page,
            itemsPerPage: meta.value.itemsPerPage,
            sort: sort.value?.key,
            sortDesc: sort.value?.order === 'desc',
            search: search.value ?? '',
            filters: filters ?? '',
        }, { arrayFormat: 'repeat' })
        fetch(`${api.value.index}?${queryString}`).then(async (r) => {
            const result = await r.json();
            total.value = result?.total ?? 0;
            items.value = result?.data ?? [];
            loading.value = false;
        }).catch((err) => {})
    }

    watchDebounced(
        [sort,meta,search,filters],
        fetchData,
        { debounce: 1000, maxWait: 2000, deep: true }
    )
    return {
        api,
        items,
        total,
        meta,
        sort,
        search,
        filters,
        loading,
        options,
        headers,

        fetchData,

        //Events
        on: {
            'update:page': (v: any) => {
                const pageOffset = 1 * (v ? 1 : 0);
                meta.value.page = v;
                from.value = ((v - 1) * meta.value.itemsPerPage) + pageOffset;
                to.value = from.value + (total.value < meta.value.itemsPerPage ? total.value : meta.value.itemsPerPage) - 1;
            },
            'update:itemsPerPage': (v: any) => {
                meta.value.itemsPerPage = v;
                meta.value.page = 1;
                meta.value.last_page = Math.ceil(total.value / v);
            },
            'update:sortBy': (v: any) => (sort.value = v[0]),
        },
    }
})
