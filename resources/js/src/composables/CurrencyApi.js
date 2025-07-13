import { ref } from "vue";
import axios from "axios";

export function CurrencyApi(dataUrl, countUrl) {
    const items = ref([]);
    const total = ref(0);
    const loading = ref(false);

    const fetchData = async (page, itemsPerPage, sortBy) => {
        loading.value = true;
        try {
            const sort = sortBy?.[0];
            const res = await axios.get(dataUrl, {
                params: {
                    page,
                    per_page: itemsPerPage,
                    sort_by: sort?.key,
                    sort_order: sort?.order,
                },
            });
            items.value = res.data.data || res.data;
        } catch (err) {
            console.error("Chyba pri fetchi dát:", err);
        } finally {
            loading.value = false;
        }
    };

    const fetchCount = async () => {
        try {
            const res = await axios.get(countUrl);
            total.value = res.data.total || res.data;
        } catch (err) {
            console.error("Chyba pri fetchi počtu:", err);
            total.value = 0;
        }
    };

    const fetchDetail = async (id) => {
        try {
            const res = await axios.get(`/currencies/${id}`);
            return res.data.data;
        } catch (error) {
            console.error("Chyba pri načítaní detailu:", error);
            return null;
        }
    };

    return {
        items,
        total,
        loading,
        fetchData,
        fetchCount,
        fetchDetail,
    };
}
