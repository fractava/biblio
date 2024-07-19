/**
 * @copyright Copyright (c) 2020 John Molakvoæ <skjnldsv@protonmail.com>
 *
 * @author John Molakvoæ <skjnldsv@protonmail.com>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

// Values
import ListFieldValue from "../components/Fields/Values/ListFieldValue.vue";
import ShortTextFieldValue from "../components/Fields/Values/ShortTextFieldValue.vue";
import LongTextFieldValue from "../components/Fields/Values/LongTextFieldValue.vue";
import DateFieldValue from "../components/Fields/Values/DateFieldValue.vue";
import SelectValue from "../components/Fields/Values/SelectValue.vue";
import CheckboxValue from "../components/Fields/Values/CheckboxValue.vue";

// Cells
import ListFieldCell from "../components/Fields/Cells/ListFieldCell.vue";
import TextCell from "../components/Fields/Cells/TextCell.vue";
import SelectCell from "../components/Fields/Cells/SelectCell.vue";
import CheckboxCell from "../components/Fields/Cells/CheckboxCell.vue";

// Settings
import NoSettings from "../components/Fields/Settings/NoSettings.vue";
import SelectSettings from "../components/Fields/Settings/SelectSettings.vue";

import FormatListNumbered from "vue-material-design-icons/FormatListNumbered.vue";
import TextShort from "vue-material-design-icons/TextShort.vue";
import TextLong from "vue-material-design-icons/TextLong.vue";
import CalendarMonth from "vue-material-design-icons/CalendarMonth.vue";
import CalendarClock from "vue-material-design-icons/CalendarClock.vue";
import FormDropdown from "vue-material-design-icons/FormDropdown.vue";
import CheckboxMarkedOutline from "vue-material-design-icons/CheckboxMarkedOutline.vue";

/**
 * @typedef {object} FieldTypes
 * @property {string} multiple
 * @property {string} short
 * @property {string} long
 * @property {string} date
 * @property {string} datetime
 */
export default {
	/**
	 * Specifying Field-Models in a common place
	 * Further type-specific parameters are possible.
	 *
	 * @property valueEditComponent The vue component to edit the value of the field
	 * @property valueCellComponent The vue component to render the field in a data table
	 * @property settingsComponent The vue component to render and edit the settings of the field
	 * @property iconComponent vue component to render the icon of the field type
	 * @property label The field-type label, that users will see as field-type.
	 * @property validate *optional* Define conditions where this field is not ok
	 * @property defaultSettings deafult settings value for new item fields
	 * @property defaultValue deafult value for new item field values
	 *
	 * @property valuePlaceholder *optional* The placeholder for value input fields
	 */

	list: {
		valueEditComponent: ListFieldValue,
		valueCellComponent: ListFieldCell,
		hasSettings: false,
		settingsComponent: NoSettings,
		iconComponent: FormatListNumbered,
		label: t("biblio", "List"),
		defaultSettings: "",
		defaultValue: [],
		canSort: false,
		filterOperators: [
			{
				id: "contains",
				label: t("biblio", "Contains"),
			},
		],
		filterOperandType: "string",
	},

	short: {
		valueEditComponent: ShortTextFieldValue,
		valueCellComponent: TextCell,
		hasSettings: false,
		settingsComponent: NoSettings,
		iconComponent: TextShort,
		label: t("biblio", "Short text"),

		valuePlaceholder: t("biblio", "Enter a short text"),
		defaultSettings: "",
		defaultValue: "",
		canSort: true,
		filterOperators: [
			{
				id: "=",
				label: t("biblio", "Equals"),
			},
			{
				id: "contains",
				label: t("biblio", "Contains"),
			},
			{
				id: "startsWith",
				label: t("biblio", "Starts with"),
			},
			{
				id: "endsWith",
				label: t("biblio", "Ends with"),
			},
		],
		filterOperandType: "string",
	},

	long: {
		valueEditComponent: LongTextFieldValue,
		valueCellComponent: TextCell,
		hasSettings: false,
		settingsComponent: NoSettings,
		iconComponent: TextLong,
		label: t("biblio", "Long text"),

		valuePlaceholder: t("biblio", "Enter a long text"),
		defaultSettings: "",
		defaultValue: "",
		canSort: true,
		filterOperators: [
			{
				id: "=",
				label: t("biblio", "Equals"),
			},
			{
				id: "contains",
				label: t("biblio", "Contains"),
			},
			{
				id: "startsWith",
				label: t("biblio", "Starts with"),
			},
			{
				id: "endsWith",
				label: t("biblio", "Ends with"),
			},
		],
		filterOperandType: "string",
	},

	date: {
		valueEditComponent: DateFieldValue,
		valueCellComponent: TextCell,
		hasSettings: false,
		settingsComponent: NoSettings,
		iconComponent: CalendarMonth,
		label: t("biblio", "Date"),

		valuePlaceholder: t("biblio", "Pick a date"),
		defaultSettings: "",
		defaultValue: "",
		canSort: true,
		filterOperators: [
			{
				id: "=",
				label: t("biblio", "Equals"),
			},
			{
				id: "smallerThan",
				label: t("biblio", "Before"),
			},
			{
				id: "largerThan",
				label: t("biblio", "After"),
			},
		],
		filterOperandType: "date",
	},

	datetime: {
		valueEditComponent: DateFieldValue,
		valueCellComponent: TextCell,
		hasSettings: false,
		settingsComponent: NoSettings,
		iconComponent: CalendarClock,
		label: t("biblio", "Date and time"),

		valuePlaceholder: t("biblio", "Pick a date and time"),
		defaultSettings: "",
		defaultValue: "",
		canSort: true,
		filterOperators: [
			{
				id: "=",
				label: t("biblio", "Equals"),
			},
			{
				id: "smallerThan",
				label: t("biblio", "Before"),
			},
			{
				id: "largerThan",
				label: t("biblio", "After"),
			},
		],
		filterOperandType: "string",

		// Using the same vue-component as date, this specifies that the component renders as datetime.
		includeTime: true,
	},

	select: {
		valueEditComponent: SelectValue,
		valueCellComponent: SelectCell,
		hasSettings: true,
		settingsComponent: SelectSettings,
		iconComponent: FormDropdown,
		label: t("biblio", "Select"),

		valuePlaceholder: t("biblio", "Pick one of the options"),
		defaultSettings: { options: [] },
		defaultValue: "",
		canSort: false,
		filterOperators: [
			{
				id: "=",
				label: t("biblio", "Equals"),
			},
		],
		filterOperandType: "options",
	},

	checkbox: {
		valueEditComponent: CheckboxValue,
		valueCellComponent: CheckboxCell,
		hasSettings: false,
		settingsComponent: NoSettings,
		iconComponent: CheckboxMarkedOutline,
		label: t("biblio", "Checkbox"),

		defaultSettings: "",
		defaultValue: false,
		canSort: true,
		filterOperators: [
			{
				id: "=",
				label: t("biblio", "Equals"),
			},
		],
		filterOperandType: "options",
		filterOperandOptions: [
			{
				id: "true",
				label: t("biblio", "Checked"),
			},
			{
				id: "false",
				label: t("biblio", "Not Checked"),
			},
		],
	},
};
