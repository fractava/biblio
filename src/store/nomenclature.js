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
		itemProperties() {
			const biblioStore = useBiblioStore();

			switch (biblioStore?.selectedCollection?.nomenclatureItem) {
			case "BOOK":
				return t("biblio", "Book Properties");
			case "DEVICE":
				return t("biblio", "Device Properties");
			default:
				return t("biblio", "Item Properties");
			}
		},
		createItem() {
			const biblioStore = useBiblioStore();

			switch (biblioStore?.selectedCollection?.nomenclatureItem) {
			case "BOOK":
				return t("biblio", "Create book");
			case "DEVICE":
				return t("biblio", "Create device");
			default:
				return t("biblio", "Create item");
			}
		},
		itemCreated() {
			return (title) => {
				const biblioStore = useBiblioStore();

				switch (biblioStore?.selectedCollection?.nomenclatureItem) {
				case "BOOK":
					return t("biblio", "Book \"{title}\" created", { title });
				case "DEVICE":
					return t("biblio", "Device \"{title}\" created", { title });
				default:
					return t("biblio", "Item \"{title}\" created", { title });
				}
			};
		},
		itemRenamed() {
			return (oldTitle, newTitle) => {
				const biblioStore = useBiblioStore();

				switch (biblioStore?.selectedCollection?.nomenclatureItem) {
				case "BOOK":
					return t("biblio", "Renamed book from \"{oldTitle}\" to \"{newTitle}\"", { oldTitle, newTitle });
				case "DEVICE":
					return t("biblio", "Renamed device from \"{oldTitle}\" to \"{newTitle}\"", { oldTitle, newTitle });
				default:
					return t("biblio", "Renamed item from \"{oldTitle}\" to \"{newTitle}\"", { oldTitle, newTitle });
				}
			};
		},
		itemDeleted() {
			return (title) => {
				const biblioStore = useBiblioStore();

				switch (biblioStore?.selectedCollection?.nomenclatureItem) {
				case "BOOK":
					return t("biblio", "Book \"{title}\" deleted", { title });
				case "DEVICE":
					return t("biblio", "Device \"{title}\" deleted", { title });
				default:
					return t("biblio", "Item \"{title}\" deleted", { title });
				}
			};
		},
		addNewItem() {
			const biblioStore = useBiblioStore();

			switch (biblioStore?.selectedCollection?.nomenclatureItem) {
			case "BOOK":
				return t("biblio", "Add new book");
			case "DEVICE":
				return t("biblio", "Add new device");
			default:
				return t("biblio", "Add new item");
			}
		},
		itemTitle() {
			const biblioStore = useBiblioStore();

			switch (biblioStore?.selectedCollection?.nomenclatureItem) {
			case "BOOK":
				return t("biblio", "Book title");
			case "DEVICE":
				return t("biblio", "Device title");
			default:
				return t("biblio", "Item title");
			}
		},
		currentlyNoItems() {
			const biblioStore = useBiblioStore();

			switch (biblioStore?.selectedCollection?.nomenclatureItem) {
			case "BOOK":
				return t("biblio", "There are currently no books in this collection");
			case "DEVICE":
				return t("biblio", "There are currently no devices in this collection");
			default:
				return t("biblio", "There are currently no items in this collection");
			}
		},
		currentlyNoItemsInSearch() {
			const biblioStore = useBiblioStore();

			switch (biblioStore?.selectedCollection?.nomenclatureItem) {
			case "BOOK":
				return t("biblio", "There are currently no books in this collection, that fit the search parameters");
			case "DEVICE":
				return t("biblio", "There are currently no devices in this collection, that fit the search parameters");
			default:
				return t("biblio", "There are currently no items in this collection, that fit the search parameters");
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
		itemInstanceCreated() {
			return (barcode) => {
				const biblioStore = useBiblioStore();

				switch (biblioStore?.selectedCollection?.nomenclatureItem) {
				case "BOOK":
					if (biblioStore?.selectedCollection?.nomenclatureInstance === "COPY") {
						return t("biblio", "Book copy \"{barcode}\" created", { barcode });
					} else {
						return t("biblio", "Book instance \"{barcode}\" created", { barcode });
					}
				case "DEVICE":
					if (biblioStore?.selectedCollection?.nomenclatureInstance === "COPY") {
						return t("biblio", "Device copy \"{barcode}\" created", { barcode });
					} else {
						return t("biblio", "Device instance \"{barcode}\" created", { barcode });
					}
				default:
					if (biblioStore?.selectedCollection?.nomenclatureInstance === "COPY") {
						return t("biblio", "Item copy \"{barcode}\" created", { barcode });
					} else {
						return t("biblio", "Item instance \"{barcode}\" created", { barcode });
					}
				}
			};
		},
		itemInstanceDeleted() {
			return (barcode) => {
				const biblioStore = useBiblioStore();

				switch (biblioStore?.selectedCollection?.nomenclatureItem) {
				case "BOOK":
					if (biblioStore?.selectedCollection?.nomenclatureInstance === "COPY") {
						return t("biblio", "Book copy \"{barcode}\" deleted", { barcode });
					} else {
						// TRANSLATORS this app has user-configured nomenclature, this combination of nomenclature settings does not make much sense, but it can be set by the user, so this string must exist. If a translation is not feasable in a language you can leave it out
						return t("biblio", "Book instance \"{barcode}\" deleted", { barcode });
					}
				case "DEVICE":
					if (biblioStore?.selectedCollection?.nomenclatureInstance === "COPY") {
						return t("biblio", "Device copy \"{barcode}\" deleted", { barcode });
					} else {
						return t("biblio", "Device instance \"{barcode}\" deleted", { barcode });
					}
				default:
					if (biblioStore?.selectedCollection?.nomenclatureInstance === "COPY") {
						return t("biblio", "Item copy \"{barcode}\" deleted", { barcode });
					} else {
						return t("biblio", "Item instance \"{barcode}\" deleted", { barcode });
					}
				}
			};
		},
		deletedItemInstance() {
			const biblioStore = useBiblioStore();

			switch (biblioStore?.selectedCollection?.nomenclatureItem) {
			case "BOOK":
				if (biblioStore?.selectedCollection?.nomenclatureInstance === "COPY") {
					// TRANSLATORS this is used as a placeholder in the history view, if a book copy does not exist anymore, but a history entry still points to the id
					return t("biblio", "[deleted book copy]");
				} else {
					// TRANSLATORS this is used as a placeholder in the history view, if a book instance does not exist anymore, but a history entry still points to the id
					return t("biblio", "[deleted book instance]");
				}
			case "DEVICE":
				if (biblioStore?.selectedCollection?.nomenclatureInstance === "COPY") {
					// TRANSLATORS this is used as a placeholder in the history view, if a device copy does not exist anymore, but a history entry still points to the id
					return t("biblio", "[deleted device copy]");
				} else {
					// TRANSLATORS this is used as a placeholder in the history view, if a device instance does not exist anymore, but a history entry still points to the id
					return t("biblio", "[deleted device instance]");
				}
			default:
				if (biblioStore?.selectedCollection?.nomenclatureInstance === "COPY") {
					// TRANSLATORS this is used as a placeholder in the history view, if a item copy does not exist anymore, but a history entry still points to the id
					return t("biblio", "[deleted item copy]");
				} else {
					// TRANSLATORS this is used as a placeholder in the history view, if a item instance does not exist anymore, but a history entry still points to the id
					return t("biblio", "[deleted item instance]");
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
		customer() {
			const biblioStore = useBiblioStore();

			switch (biblioStore?.selectedCollection?.nomenclatureCustomer) {
			case "STUDENT":
				return t("biblio", "Student");
			case "EMPLOYEE":
				return t("biblio", "Employee");
			default:
				return t("biblio", "Customer");
			}
		},
		customerProperties() {
			const biblioStore = useBiblioStore();

			switch (biblioStore?.selectedCollection?.nomenclatureCustomer) {
			case "STUDENT":
				return t("biblio", "Student Properties");
			case "EMPLOYEE":
				return t("biblio", "Employee Properties");
			default:
				return t("biblio", "Customer Properties");
			}
		},
		customerLoans() {
			const biblioStore = useBiblioStore();

			switch (biblioStore?.selectedCollection?.nomenclatureCustomer) {
			case "STUDENT":
				return t("biblio", "Student Loans");
			case "EMPLOYEE":
				return t("biblio", "Employee Loans");
			default:
				return t("biblio", "Customer Loans");
			}
		},
		createCustomer() {
			const biblioStore = useBiblioStore();

			switch (biblioStore?.selectedCollection?.nomenclatureCustomer) {
			case "STUDENT":
				return t("biblio", "Create student");
			case "EMPLOYEE":
				return t("biblio", "Create employee");
			default:
				return t("biblio", "Create customer");
			}
		},
		customerCreated() {
			return (name) => {
				const biblioStore = useBiblioStore();

				switch (biblioStore?.selectedCollection?.nomenclatureCustomer) {
				case "STUDENT":
					return t("biblio", "Student \"{name}\" created", { name });
				case "EMPLOYEE":
					return t("biblio", "Employee \"{name}\" created", { name });
				default:
					return t("biblio", "Customer \"{name}\" created", { name });
				}
			};
		},
		customerRenamed() {
			return (oldName, newName) => {
				const biblioStore = useBiblioStore();

				switch (biblioStore?.selectedCollection?.nomenclatureCustomer) {
				case "STUDENT":
					return t("biblio", "Student renamed from \"{oldName}\" to \"{newName}\"", { oldName, newName });
				case "EMPLOYEE":
					return t("biblio", "Employee renamed from \"{oldName}\" to \"{newName}\"", { oldName, newName });
				default:
					return t("biblio", "Customer renamed from \"{oldName}\" to \"{newName}\"", { oldName, newName });
				}
			};
		},
		customerDeleted() {
			return (name) => {
				const biblioStore = useBiblioStore();

				switch (biblioStore?.selectedCollection?.nomenclatureCustomer) {
				case "STUDENT":
					return t("biblio", "Student \"{name}\" deleted", { name });
				case "EMPLOYEE":
					return t("biblio", "Employee \"{name}\" deleted", { name });
				default:
					return t("biblio", "Customer \"{name}\" deleted", { name });
				}
			};
		},
		deletedCustomer() {
			const biblioStore = useBiblioStore();

			switch (biblioStore?.selectedCollection?.nomenclatureCustomer) {
			case "STUDENT":
				// TRANSLATORS this is used as a placeholder in the history view, if a student does not exist anymore, but a history entry still points to the id
				return t("biblio", "[deleted student]");
			case "EMPLOYEE":
				// TRANSLATORS this is used as a placeholder in the history view, if an employee does not exist anymore, but a history entry still points to the id
				return t("biblio", "[deleted employee]");
			default:
				// TRANSLATORS this is used as a placeholder in the history view, if a customer does not exist anymore, but a history entry still points to the id
				return t("biblio", "[deleted customer]");
			}
		},
		addNewCustomer() {
			const biblioStore = useBiblioStore();

			switch (biblioStore?.selectedCollection?.nomenclatureCustomer) {
			case "STUDENT":
				return t("biblio", "Add new student");
			case "EMPLOYEE":
				return t("biblio", "Add new employee");
			default:
				return t("biblio", "Add new customer");
			}
		},
		noCustomers() {
			const biblioStore = useBiblioStore();

			switch (biblioStore?.selectedCollection?.nomenclatureCustomer) {
			case "STUDENT":
				return t("biblio", "There are currently no students in this collection, that fit the search parameters");
			case "EMPLOYEE":
				return t("biblio", "There are currently no employees in this collection, that fit the search parameters");
			default:
				return t("biblio", "There are currently no customers in this collection, that fit the search parameters");
			}
		},
		loanedToCustomer() {
			const biblioStore = useBiblioStore();

			switch (biblioStore?.selectedCollection?.nomenclatureCustomer) {
			case "STUDENT":
				return t("biblio", "Loaned to student");
			case "EMPLOYEE":
				return t("biblio", "Loaned to employee");
			default:
				return t("biblio", "Loaned to customer");
			}
		},
		loanCreated() {
			return (barcode, customerName, untilFormattedDate) => {
				const biblioStore = useBiblioStore();

				switch (biblioStore?.selectedCollection?.nomenclatureItem) {
				case "BOOK":
					if (biblioStore?.selectedCollection?.nomenclatureInstance === "COPY") {
						return t("biblio", "Book copy \"{barcode}\" loaned to \"{customerName}\" until {untilFormattedDate}", { barcode, customerName, untilFormattedDate });
					} else {
						return t("biblio", "Book instance \"{barcode}\" loaned to \"{customerName}\" until {untilFormattedDate}", { barcode, customerName, untilFormattedDate });
					}
				case "DEVICE":
					if (biblioStore?.selectedCollection?.nomenclatureInstance === "COPY") {
						return t("biblio", "Device copy \"{barcode}\" loaned to \"{customerName}\" until {untilFormattedDate}", { barcode, customerName, untilFormattedDate });
					} else {
						return t("biblio", "Device instance \"{barcode}\" loaned to \"{customerName}\" until {untilFormattedDate}", { barcode, customerName, untilFormattedDate });
					}
				default:
					if (biblioStore?.selectedCollection?.nomenclatureInstance === "COPY") {
						return t("biblio", "Item copy \"{barcode}\" loaned to \"{customerName}\" until {untilFormattedDate}", { barcode, customerName, untilFormattedDate });
					} else {
						return t("biblio", "Item instance \"{barcode}\" loaned to \"{customerName}\" until {untilFormattedDate}", { barcode, customerName, untilFormattedDate });
					}
				}
			};
		},
		loanDeleted() {
			return (barcode, customerName) => {
				const biblioStore = useBiblioStore();

				switch (biblioStore?.selectedCollection?.nomenclatureItem) {
				case "BOOK":
					if (biblioStore?.selectedCollection?.nomenclatureInstance === "COPY") {
						return t("biblio", "Book copy \"{barcode}\" returned by \"{customerName}\"", { barcode, customerName });
					} else {
						return t("biblio", "Book instance \"{barcode}\" returned by \"{customerName}\"", { barcode, customerName });
					}
				case "DEVICE":
					if (biblioStore?.selectedCollection?.nomenclatureInstance === "COPY") {
						return t("biblio", "Device copy \"{barcode}\" returned by \"{customerName}\"", { barcode, customerName });
					} else {
						return t("biblio", "Device instance \"{barcode}\" returned by \"{customerName}\"", { barcode, customerName });
					}
				default:
					if (biblioStore?.selectedCollection?.nomenclatureInstance === "COPY") {
						return t("biblio", "Item copy \"{barcode}\" returned by \"{customerName}\"", { barcode, customerName });
					} else {
						return t("biblio", "Item instance \"{barcode}\" returned by \"{customerName}\"", { barcode, customerName });
					}
				}
			};
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
		itemFields() {
			const biblioStore = useBiblioStore();

			switch (biblioStore?.selectedCollection?.nomenclatureItem) {
			case "BOOK":
				return t("biblio", "Book fields");
			case "DEVICE":
				return t("biblio", "Device fields");
			default:
				return t("biblio", "Item fields");
			}
		},
		itemFieldCreated() {
			return (name, type) => {
				const biblioStore = useBiblioStore();

				switch (biblioStore?.selectedCollection?.nomenclatureItem) {
				case "BOOK":
					return t("biblio", "Book field \"{name}\" of type {type} created", { name, type });
				case "DEVICE":
					return t("biblio", "Device field \"{name}\" of type {type} created", { name, type });
				default:
					return t("biblio", "Item field \"{name}\" of type {type} created", { name, type });
				}
			};
		},
		itemFieldUpdated() {
			return (name) => {
				const biblioStore = useBiblioStore();

				switch (biblioStore?.selectedCollection?.nomenclatureItem) {
				case "BOOK":
					return t("biblio", "Book field \"{name}\" updated:", { name });
				case "DEVICE":
					return t("biblio", "Device field \"{name}\" updated:", { name });
				default:
					return t("biblio", "Item field \"{name}\" updated:", { name });
				}
			};
		},
		itemFieldDeleted() {
			return (name) => {
				const biblioStore = useBiblioStore();

				switch (biblioStore?.selectedCollection?.nomenclatureItem) {
				case "BOOK":
					return t("biblio", "Book field \"{name}\" deleted", { name });
				case "DEVICE":
					return t("biblio", "Device field \"{name}\" deleted", { name });
				default:
					return t("biblio", "Item field \"{name}\" deleted", { name });
				}
			};
		}
	},
});
