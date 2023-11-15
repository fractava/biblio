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
			case "customer.update": {
				return this.nomenclatureStore.customerRenamed(this.value?.properties?.before?.name, this.value?.properties?.after?.name);
			}
			case "customer.delete": {
				return this.nomenclatureStore.customerDeleted(this.value?.properties?.before?.name);
			}
			case "item.create": {
				return this.nomenclatureStore.itemCreated(this.value?.properties?.after?.title);
			}
			case "item.update": {
				return this.nomenclatureStore.itemRenamed(this.value?.properties?.before?.title, this.value?.properties?.after?.title);
			}
			case "itemInstance.create": {
				return this.nomenclatureStore.itemInstanceCreated(this.value?.properties?.after?.barcode);
			}
			case "loan.create": {
				const itemInstanceBarcode = this.value?.itemInstance?.barcode || this.nomenclatureStore.deletedItemInstance();
				const customerName = this.value?.customer?.name || this.nomenclatureStore.deletedCustomer();

				const untilDate = new Date(this.value?.properties?.after?.until * 1000);
				const untilFormattedDate = new Intl.DateTimeFormat(getCanonicalLocale()).format(untilDate);

				return this.nomenclatureStore.loanCreated(itemInstanceBarcode, customerName, untilFormattedDate);
			}
			case "itemField.create": {
				return this.nomenclatureStore.itemFieldCreated(this.value?.properties?.after?.name, this.value?.properties?.after?.type);
			}
			case "itemField.update": {
				let description = this.nomenclatureStore.itemFieldUpdated(this.value?.properties?.before?.name) + " ";

				const changes = [];

				if (this.value?.properties?.after?.name !== this.value?.properties?.before?.name) {
					changes.push(t("biblio", "Renamed to \"{newName}\"", { newName: this.value?.properties?.after?.name }));
				}

				if (this.value?.properties?.after?.includeInList !== this.value?.properties?.before?.includeInList) {
					if (this.value?.properties?.after?.includeInList) {
						changes.push(t("biblio", "Enabled visibility in list views"));
					} else {
						changes.push(t("biblio", "Disabled visibility in list views"));
					}
				}

				if (this.value?.properties?.after?.settings !== this.value?.properties?.before?.settings) {
					changes.push(t("biblio", "Changed settings"));
				}

				if (changes.length === 0) {
					description += t("biblio", "No changes");
				} else {
					description += changes.join(", ");
				}

				return description;
			}
			case "itemField.delete": {
				return this.nomenclatureStore.itemFieldDeleted(this.value?.properties?.before?.name);
			}
			default:
				return this.value?.type;
			}
		},
	},
};
</script>
