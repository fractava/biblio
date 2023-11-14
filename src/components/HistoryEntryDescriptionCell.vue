<template>
	<span>
		{{ message }}
	</span>
</template>
<script>
import { mapStores } from "pinia";

import { useNomenclatureStore } from "../store/nomenclature.js";

import { getCanonicalLocale } from "@nextcloud/l10n";

export default {
	props: ["value"],
	computed: {
		...mapStores(useNomenclatureStore),
		message() {
			switch (this.value?.type) {
			case "customer.create": {
				return this.nomenclatureStore.customerCreated(this.value?.properties?.after?.name);
			}
			case "item.create": {
				return this.nomenclatureStore.itemCreated(this.value?.properties?.after?.title);
			}
			case "item.update": {
				return this.nomenclatureStore.itemRenamed(this.value?.properties?.before?.title, this.value?.properties?.after?.title);
			}
			case "itemInstance.create": {
				return this.nomenclatureStore.createdInstance(this.value?.properties?.after?.barcode);
			}
			case "loan.create": {
				const itemInstanceBarcode = this.value?.itemInstance?.barcode || t("biblio", "[deleted item instance]");
				const customerNameBarcode = this.value?.customer?.name || t("biblio", "[deleted customer]");
				const untilDate = new Date(this.value?.properties?.after?.until * 1000);
				const untilFormattedDate = new Intl.DateTimeFormat(getCanonicalLocale()).format(untilDate);

				return this.nomenclatureStore.createdLoan(itemInstanceBarcode, customerNameBarcode, untilFormattedDate);
			}
			default:
				return this.value?.type;
			}
		},
	},
};
</script>
