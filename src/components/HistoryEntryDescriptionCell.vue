<template>
	<div>
		{{ message }}
	</div>
</template>
<script>
import { mapStores } from "pinia";

import { useNomenclatureStore } from "../store/nomenclature.js";

export default {
	props: ["value"],
	computed: {
		...mapStores(useNomenclatureStore),
		message() {
			switch (this.value?.type) {
			case "customer.create":
				return this.nomenclatureStore.customerCreated(this.value?.properties?.after?.name);
			case "item.create":
				return this.nomenclatureStore.itemCreated(this.value?.properties?.after?.title);
			default:
				return this.value?.type;
			}
		},
	},
};
</script>
