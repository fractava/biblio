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
			case "item.delete": {
				return this.nomenclatureStore.itemDeleted(this.value?.properties?.before?.title);
			}
			case "itemInstance.create": {
				return this.nomenclatureStore.itemInstanceCreated(this.value?.properties?.after?.barcode);
			}
			case "itemInstance.delete": {
				return this.nomenclatureStore.itemInstanceDeleted(this.value?.properties?.before?.barcode);
			}
			case "loan.create": {
				const itemInstanceBarcode = this.value?.itemInstance?.barcode || this.nomenclatureStore.deletedItemInstance();
				const customerName = this.value?.customer?.name || this.nomenclatureStore.deletedCustomer();

				const untilDate = new Date(this.value?.properties?.after?.until * 1000);
				const untilFormattedDate = new Intl.DateTimeFormat(getCanonicalLocale()).format(untilDate);

				return this.nomenclatureStore.loanCreated(itemInstanceBarcode, customerName, untilFormattedDate);
			}
			case "loan.delete": {
				const itemInstanceBarcode = this.value?.itemInstance?.barcode || this.nomenclatureStore.deletedItemInstance();
				const customerName = this.value?.customer?.name || this.nomenclatureStore.deletedCustomer();

				return this.nomenclatureStore.loanDeleted(itemInstanceBarcode, customerName);
			}
			case "itemField.create": {
				return this.nomenclatureStore.itemFieldCreated(this.value?.properties?.after?.name, this.value?.properties?.after?.type);
			}
			case "itemField.update": {
				let description = this.nomenclatureStore.itemFieldUpdated(this.value?.properties?.before?.name) + " ";

				let changes = this.genericFieldUpdates(this.value?.properties?.before, this.value?.properties?.after)

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
			case "loanField.create": {
				return this.nomenclatureStore.loanFieldCreated(this.value?.properties?.after?.name, this.value?.properties?.after?.type);
			}
			case "loanField.update": {
				let description = this.nomenclatureStore.loanFieldUpdated(this.value?.properties?.before?.name) + " ";

				let changes = this.genericFieldUpdates(this.value?.properties?.before, this.value?.properties?.after)

				if (changes.length === 0) {
					description += t("biblio", "No changes");
				} else {
					description += changes.join(", ");
				}

				return description;
			}
			case "loanField.delete": {
				return this.nomenclatureStore.loanFieldDeleted(this.value?.properties?.before?.name);
			}
			default:
				return this.value?.type;
			}
		},
	},
	methods: {
		// works with item fields, customer fields, loan fields
		genericFieldUpdates(before, after) {
			const changes = [];

			if (after?.name !== before?.name) {
				changes.push(t("biblio", "Renamed to \"{newName}\"", { newName: after?.name }));
			}

			if (after?.includeInList !== before?.includeInList) {
				if (after?.includeInList) {
					changes.push(t("biblio", "Enabled visibility in list views"));
				} else {
					changes.push(t("biblio", "Disabled visibility in list views"));
				}
			}

			if (after?.settings !== before?.settings) {
				changes.push(t("biblio", "Changed settings"));
			}

			return changes;
		}
	},
};
</script>
