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

import ListField from "../components/Fields/ListField";
import ShortTextFieldValue from "../components/Fields/ShortTextFieldValue";
import LongTextFieldValue from "../components/Fields/LongTextFieldValue";
import DateField from "../components/Fields/DateField";

import NoSettings from "../components/Fields/NoSettings";

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
	 * @property component The vue-component this field-type relies on
	 * @property icon The icon corresponding to this field-type
	 * @property label The field-type label, that users will see as field-type.
	 * @property validate *optional* Define conditions where this field is not ok
	 *
	 * @property valuePlaceholder *optional* The placeholder for value input fields
	 */

	multiple: {
		component: ListField,
		icon: "icon-list-field",
		// TRANSLATORS Take care, a translation by word might not match! The english called 'Multiple-Choice' only allows to select a single-option (basically single-choice)!
		label: t("biblio", "List"),
		defaultSettings: "",
		defaultValue: [],
	},

	short: {
		valueComponent: ShortTextFieldValue,
		settingsComponent: NoSettings,
		icon: "icon-short-text-field",
		label: t("biblio", "Short text"),

		valuePlaceholder: t("biblio", "Enter a short text"),
		defaultSettings: "",
		defaultValue: "",
	},

	long: {
		valueComponent: LongTextFieldValue,
		settingsComponent: NoSettings,
		icon: "icon-long-text-field",
		label: t("biblio", "Long text"),

		valuePlaceholder: t("biblio", "Enter a long text"),
		defaultSettings: "",
		defaultValue: "",
	},

	date: {
		component: DateField,
		icon: "icon-date-field",
		label: t("biblio", "Date"),

		valuePlaceholder: t("biblio", "Pick a date"),
		defaultSettings: "",
		defaultValue: "",
	},

	datetime: {
		component: DateField,
		icon: "icon-datetime-field",
		label: t("biblio", "Date and time"),

		valuePlaceholder: t("biblio", "Pick a date and time"),
		defaultSettings: "",
		defaultValue: "",

		// Using the same vue-component as date, this specifies that the component renders as datetime.
		includeTime: true,
	},

	// TODO: Select
};
