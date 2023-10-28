import { defineStore } from "pinia";

import { useBiblioStore } from "./biblio.js";

export const useNomenclatureStore = defineStore("nomenclature", {
	state: () => ({
		options: {
			item: [
				{
					id: "ITEM",
					label: t("biblio", "Item"),
				},
				{
					id: "BOOK",
					label: t("biblio", "Book"),
				},
				{
					id: "DEVICE",
					label: t("biblio", "Device"),
				},
			],
			instance: [
				{
					id: "INSTANCE",
					label: t("biblio", "Instance"),
				},
				{
					id: "COPY",
					label: t("biblio", "Copy"),
				},
			],
			customer: [
				{
					id: "CUSTOMER",
					label: t("biblio", "Customer"),
				},
				{
					id: "STUDENT",
					label: t("biblio", "Student"),
				},
				{
					id: "EMPLOYEE",
					label: t("biblio", "Employee"),
				},
			],
		},
	}),
	getters: {
		createItem() {
			const biblioStore = useBiblioStore();

			switch (biblioStore?.selectedCollection?.nomenclatureItem) {
			case "BOOK":
				return t("biblio", "Create Book");
			case "DEVICE":
				return t("biblio", "Create Device");
			default:
				return t("biblio", "Create Item");
			}
		},
	},
});
