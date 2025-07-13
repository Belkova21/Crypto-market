<template>
    <div class="flex mt-8 mb-4 pl-6">
        <h1 class="text-4xl font-extrabold text-gray-800 tracking-tight">
            Crypto Market Watch
        </h1>
    </div>
    <CurrencyTable
        :data-url="'/currencies'"
        :count-url="'/currencies/total'"
        :headers="tableHeaders"
    />
</template>

<script setup>
import CurrencyTable from "../components/CurrencyTable/CurrencyTable.vue";
const tableHeaders = [
    { key: "name", title: "Name", sortable: false },
    { key: "current_price", title: "Price", sortable: false },
    { key: "price_change_percentage_1h", title: "1h %", sortable: false },
    { key: "price_change_percentage_24h", title: "24h %", sortable: false },
    { key: "price_change_percentage_7d", title: "7d %", sortable: false },
    { key: "market_cap", title: "Market Cap" },
    { key: "volume", title: "Volume" },
    { key: "sparkline_in_7d", title: "Last 7 days", sortable: false },
];
</script>
