import FieldTypes from "../models/FieldTypes.js";
import TextCell from "../components/Fields/Cells/TextCell.vue";
import TimestampCell from "../components/TimestampCell.vue";

import { useNomenclatureStore } from "../store/nomenclature.js";

function getMaxPage(total, limit) {
	return Math.ceil(Math.max(total, 1) / limit) || 1;
}

function getFieldColumns(sortedFields = []) {
	return sortedFields.map((field) => {
		const type = FieldTypes[field.type];
		return {
			id: field.id,
			name: field.name,
			type: field.type,
			isProperty: false,
			canSort: type.canSort,
			sortIdentifier: `field:${field.id}`,
			canFilter: true,
			filterOperators: type.filterOperators,
			filterOperandType: type.filterOperandType,
			filterOperandOptions: type.filterOperandOptions || field?.settings?.options,
			cellComponent: type.valueCellComponent,
			defaultValue: type.defaultValue,
			defaultSettings: type.defaultSettings,
		};
	});
}

function getItemInstanceColumns(includeBarcode = true, includeItemTitle = true, includeLoanedCustomerName = true, includeLoanedUntil = true, fields = []) {
	const nomenclatureStore = useNomenclatureStore();

	const fieldColumns = getFieldColumns(fields);
	const result = [];

	if (includeBarcode) {
		result.push({
			id: -1,
			name: t("biblio", "Barcode"),
			type: "short",
			isProperty: true,
			canSort: true,
			sortIdentifier: "barcode",
			canFilter: false,
			clickable: false,
			property: "barcode",
			cellComponent: TextCell,
		});
	}

	if (includeItemTitle) {
		result.push({
			id: -2,
			name: nomenclatureStore.itemTitle,
			type: "short",
			isProperty: true,
			canSort: true,
			sortIdentifier: "item_title",
			canFilter: true,
			filterOperators: FieldTypes.short.filterOperators,
			filterOperandType: FieldTypes.short.filterOperandType,
			clickable: true,
			property: ["item", "title"],
			cellComponent: TextCell,
		});
	}

	if (includeLoanedCustomerName) {
		result.push({
			id: -3,
			name: nomenclatureStore.loanedToCustomer,
			type: "short",
			isProperty: true,
			canSort: true,
			sortIdentifier: "loan_customer_name",
			canFilter: true,
			filterOperators: FieldTypes.short.filterOperators,
			filterOperandType: FieldTypes.short.filterOperandType,
			clickable: true,
			property: ["loan", "customer", "name"],
			cellComponent: TextCell,
		});
	}

	if (includeLoanedUntil) {
		result.push({
			id: -4,
			name: t("biblio", "Loaned Until"),
			type: "date",
			isProperty: true,
			canSort: true,
			sortIdentifier: "loan_until",
			canFilter: true,
			filterOperators: FieldTypes.date.filterOperators,
			filterOperandType: FieldTypes.date.filterOperandType,
			clickable: true,
			property: ["loan", "until"],
			cellComponent: TimestampCell,
		});
	}

	return [
		...result,
		...fieldColumns,
	];
}

function firstItemIndex(page, limit) {
	return ((page - 1) * limit) + 1;
}

function lastItemIndex(firstItemIndex, searchResultsLength) {
	return firstItemIndex + searchResultsLength - 1;
}

export { getMaxPage, getFieldColumns, getItemInstanceColumns, firstItemIndex, lastItemIndex };
