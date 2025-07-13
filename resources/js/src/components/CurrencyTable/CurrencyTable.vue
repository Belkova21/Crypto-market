<template>
    <div class="flex w-full">
        <div :class="selectedCoin ? 'w-3/5' : 'w-full'">
            <v-data-table-server
                :headers="headers"
                :items="items"
                :items-length="total"
                :loading="loading"
                v-model:page="page"
                v-model:items-per-page="itemsPerPage"
                v-model:sort-by="sortBy"
                class="custom-table"
                @click:row="onRowClick"
            >
                <template #item.name="{ item }">
                    <div class="name-cell">
                        <img :src="item.image" class="coin-logo" />
                        <div>
                            <div>{{ item.name }}</div>
                            <div class="symbol">
                                {{ item.symbol.toUpperCase() }}
                            </div>
                        </div>
                    </div>
                </template>
                <template #item.price_change_percentage_1h="{ item }">
                    <ColumnPercentageFormat
                        :value="item.price_change_percentage_1h"
                    />
                </template>

                <template #item.price_change_percentage_24h="{ item }">
                    <ColumnPercentageFormat
                        :value="item.price_change_percentage_24h"
                    />
                </template>

                <template #item.price_change_percentage_7d="{ item }">
                    <ColumnPercentageFormat
                        :value="item.price_change_percentage_7d"
                    />
                </template>

                <template #item.sparkline_in_7d="{ item }">
                    <ApexChart
                        v-if="item.sparkline_in_7d"
                        :options="getSparklineOptions(item.sparkline_in_7d)"
                        :series="[{ data: item.sparkline_in_7d }]"
                        type="line"
                        height="40"
                        width="100"
                    />
                </template>
            </v-data-table-server>
        </div>
        <div v-if="selectedCoin" class="w-px bg-gray-200 mx-2"></div>

        <div v-if="selectedCoin" class="w-2/5">
            <CurrencyDetail
                :data="selectedCoin"
                @close="selectedCoin = null"
            ></CurrencyDetail>
        </div>
    </div>
</template>

<script setup>
import { onMounted, onUnmounted, ref, watch } from "vue";
import ApexChart from "vue3-apexcharts";
import CurrencyDetail from "../CurrencyDetail/CurrencyDetail.vue";
import ColumnPercentageFormat from "./ColumnPercentageFormat.vue";
import { CurrencyApi } from "../../composables/CurrencyApi.js";
import Echo from "laravel-echo";

const props = defineProps({
    dataUrl: { type: String, required: true },
    countUrl: { type: String, required: true },
    headers: { type: Array, required: true },
});

// stav
const page = ref(1);
const itemsPerPage = ref(10);
const sortBy = ref([{ key: "market_cap", order: "desc" }]);
const selectedCoin = ref(null);
let channel = null;

// data
const { items, total, loading, fetchData, fetchCount, fetchDetail } =
    CurrencyApi(props.dataUrl, props.countUrl);

watch([page, sortBy], () => {
    fetchTableData();
});

const fetchTableData = async () => {
    try {
        await fetchData(page.value, itemsPerPage.value, sortBy.value);
    } catch (err) {
        console.error("Chyba pri načítavaní dát:", err);
    }
};

const fetchTotalCount = async () => {
    try {
        await fetchCount();
    } catch (err) {
        console.error("Chyba pri načítavaní počtu záznamov:", err);
    }
};

const onRowClick = async (event, row) => {
    if (!row?.item?.id) return;

    if (!selectedCoin.value?.charts || row.item.id !== selectedCoin.value?.id) {
        try {
            row.item.charts = await fetchDetail(row.item.id);
            selectedCoin.value = row.item;
        } catch (err) {
            console.error("Chyba pri získavaní detailu meny:", err);
        }
    }
};

// aktualizácia cez WS
const handleCurrencyUpdate = (currenciesUpdate) => {
    console.log("Update from WS : ", currenciesUpdate);
    currenciesUpdate.forEach((updatedCoin) => {
        const index = items.value.findIndex(
            (coin) => coin.id === updatedCoin.id,
        );
        if (index !== -1) {
            Object.assign(items.value[index], updatedCoin);
        }
    });
};

// mount/unmount
onMounted(() => {
    fetchTableData();
    fetchTotalCount();

    channel = window.Echo.channel("currencies");
    channel.listen(".currency.updated", (e) => {
        handleCurrencyUpdate(e.currencies);
    });
});

onUnmounted(() => {
    if (channel) {
        channel.stopListening(".currency.updated");
        window.Echo.leave("currencies");
    }
});

// graf
const getSparklineOptions = (data) => {
    const color =
        data && data.length > 1 && data[data.length - 1] < data[0]
            ? "#EA3943"
            : "#16c784";
    return {
        chart: { type: "line", sparkline: { enabled: true } },
        stroke: { curve: "smooth", width: 2 },
        tooltip: { enabled: false },
        colors: [color],
    };
};
</script>

<style scoped>
.name-cell {
    display: flex;
    align-items: center;
}
.coin-logo {
    width: 24px;
    height: 24px;
    margin-right: 8px;
    border-radius: 50%;
}
.symbol {
    font-size: 0.8em;
    color: #666;
}
::v-deep(.v-data-table-footer) {
    justify-content: center !important;
}
::v-deep(.v-data-table-footer__items-per-page) {
    display: none !important;
}
::v-deep(.v-data-table-footer) {
    justify-content: center !important;
}
</style>
