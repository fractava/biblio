import { defineStore } from "pinia";

import { useBiblioStore } from "./biblio.js";

import RadioboxBlank from "vue-material-design-icons/RadioboxBlank.vue";
// import BookOpenPageVariant from "vue-material-design-icons/BookOpenPageVariant.vue";
import Book from "vue-material-design-icons/Book.vue";
import Laptop from "vue-material-design-icons/Laptop.vue";
import BookMultiple from "vue-material-design-icons/BookMultiple.vue";
import Account from "vue-material-design-icons/Account.vue";
import AccountSchool from "vue-material-design-icons/AccountSchool.vue";
import AccountTie from "vue-material-design-icons/AccountTie.vue";

export const useNomenclatureStore = defineStore("nomenclature", {
	state: () => ({
		options: {
			item: [
				{
					id: "ITEM",
					label: t("biblio", "Item"),
					icon: RadioboxBlank,
				},
				{
					id: "BOOK",
					label: t("biblio", "Book"),
					icon: Book,
				},
				{
					id: "DEVICE",
					label: t("biblio", "Device"),
					icon: Laptop,
				},
			],
			instance: [
				{
					id: "INSTANCE",
					label: t("biblio", "Instance"),
					icon: RadioboxBlank,
				},
				{
					id: "COPY",
					label: t("biblio", "Copy"),
					icon: BookMultiple,
				},
			],
			customer: [
				{
					id: "CUSTOMER",
					label: t("biblio", "Customer"),
					icon: Account,
				},
				{
					id: "STUDENT",
					label: t("biblio", "Student"),
					icon: AccountSchool,
				},
				{
					id: "EMPLOYEE",
					label: t("biblio", "Employee"),
					icon: AccountTie,
				},
			],
		},
	}),
	getters: {
		items() {
			const biblioStore = useBiblioStore();

			switch (biblioStore?.selectedCollection?.nomenclatureItem) {
			case "BOOK":
				return t("biblio", "Books");
			case "DEVICE":
				return t("biblio", "Devices");
			default:
				return t("biblio", "Items");
			}
		},
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
		addNewItem() {
			const biblioStore = useBiblioStore();

			switch (biblioStore?.selectedCollection?.nomenclatureItem) {
			case "BOOK":
				return t("biblio", "Add new Book");
			case "DEVICE":
				return t("biblio", "Add new Device");
			default:
				return t("biblio", "Add new Item");
			}
		},
		instances() {
			const biblioStore = useBiblioStore();

			switch (biblioStore?.selectedCollection?.nomenclatureItem) {
			case "BOOK":
				if (biblioStore?.selectedCollection?.nomenclatureInstance === "COPY") {
					return t("biblio", "Book copies");
				} else {
					return t("biblio", "Book instances");
				}
			case "DEVICE":
				if (biblioStore?.selectedCollection?.nomenclatureInstance === "COPY") {
					return t("biblio", "Device copies");
				} else {
					return t("biblio", "Device instances");
				}
			default:
				if (biblioStore?.selectedCollection?.nomenclatureInstance === "COPY") {
					return t("biblio", "Item copies");
				} else {
					return t("biblio", "Item instances");
				}
			}
		},
		customers() {
			const biblioStore = useBiblioStore();

			switch (biblioStore?.selectedCollection?.nomenclatureCustomer) {
			case "STUDENT":
				return t("biblio", "Students");
			case "EMPLOYEE":
				return t("biblio", "Employees");
			default:
				return t("biblio", "Customers");
			}
		},
		addNewCustomer() {
			const biblioStore = useBiblioStore();

			switch (biblioStore?.selectedCollection?.nomenclatureCustomer) {
			case "STUDENT":
				return t("biblio", "Add new Student");
			case "EMPLOYEE":
				return t("biblio", "Add new Employee");
			default:
				return t("biblio", "Add new Customer");
			}
		},
		itemIcon() {
			const biblioStore = useBiblioStore();

			switch (biblioStore?.selectedCollection?.nomenclatureItem) {
			case "BOOK":
				return Book;
			case "DEVICE":
				return Laptop;
			default:
				return RadioboxBlank;
			}
		},
		instanceIcon() {
			const biblioStore = useBiblioStore();

			switch (biblioStore?.selectedCollection?.nomenclatureInstance) {
			case "COPY":
				return BookMultiple;
			default:
				return RadioboxBlank;
			}
		},
		customerIcon() {
			const biblioStore = useBiblioStore();

			switch (biblioStore?.selectedCollection?.nomenclatureCustomer) {
			case "STUDENT":
				return AccountSchool;
			case "EMPLOYEE":
				return AccountTie;
			default:
				return Account;
			}
		},
	},
});
