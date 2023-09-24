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

import ListFieldValue from "../components/Fields/ListFieldValue.vue";
import ShortTextFieldValue from "../components/Fields/ShortTextFieldValue.vue";
import LongTextFieldValue from "../components/Fields/LongTextFieldValue.vue";
import DateFieldValue from "../components/Fields/DateFieldValue.vue";

import NoSettings from "../components/Fields/NoSettings.vue";

import FormatListNumbered from "vue-material-design-icons/FormatListNumbered";
import TextShort from "vue-material-design-icons/TextShort";
import TextLong from "vue-material-design-icons/TextLong";
import CalendarMonth from "vue-material-design-icons/CalendarMonth";
import CalendarClock from "vue-material-design-icons/CalendarClock";

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

	list: {
		valueComponent: ListFieldValue,
		settingsComponent: NoSettings,
		iconComponent: FormatListNumbered,
		label: t("biblio", "List"),
		defaultSettings: "",
		defaultValue: [],
	},

	short: {
		valueComponent: ShortTextFieldValue,
		settingsComponent: NoSettings,
		iconComponent: TextShort,
		label: t("biblio", "Short text"),

		valuePlaceholder: t("biblio", "Enter a short text"),
		defaultSettings: "",
		defaultValue: "",
	},

	long: {
		valueComponent: LongTextFieldValue,
		settingsComponent: NoSettings,
		iconComponent: TextLong,
		label: t("biblio", "Long text"),

		valuePlaceholder: t("biblio", "Enter a long text"),
		defaultSettings: "",
		defaultValue: "",
	},

	date: {
		component: DateFieldValue,
		settingsComponent: NoSettings,
		iconComponent: CalendarMonth,
		label: t("biblio", "Date"),

		valuePlaceholder: t("biblio", "Pick a date"),
		defaultSettings: "",
		defaultValue: "",
	},

	datetime: {
		component: DateFieldValue,
		settingsComponent: NoSettings,
		iconComponent: CalendarClock,
		label: t("biblio", "Date and time"),

		valuePlaceholder: t("biblio", "Pick a date and time"),
		defaultSettings: "",
		defaultValue: "",

		// Using the same vue-component as date, this specifies that the component renders as datetime.
		includeTime: true,
	},

	// TODO: Select
};
