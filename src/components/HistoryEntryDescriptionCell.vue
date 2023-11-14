<template>
	<span>
		{{ message }}
	</span>
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
			case "item.update":
				return this.nomenclatureStore.itemRenamed(this.value?.properties?.before?.title, this.value?.properties?.after?.title);
			case "itemInstance.create":
				return this.nomenclatureStore.createdInstance(this.value?.properties?.after?.barcode);
			case "loan.create":
				return this.nomenclatureStore.createdLoan(this.value?.itemInstanceId, this.value?.customerId, this.value?.properties?.after?.until);
			default:
				return this.value?.type;
			}
		},
	},
};
</script>
