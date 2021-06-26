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

import QuestionMultiple from '../components/Questions/QuestionMultiple'
import QuestionShort from '../components/Questions/QuestionShort'
import QuestionLong from '../components/Questions/QuestionLong'
import QuestionDate from '../components/Questions/QuestionDate'

/**
 * @typedef {Object} AnswerTypes
 * @property {string} multiple_unique
 * @property {string} short
 * @property {string} long
 * @property {string} date
 * @property {string} datetime
 */
export default {
	/**
	 * !! Keep in SYNC with lib/Constants.php for props that are necessary on php !!
	 * Specifying Question-Models in a common place
	 * Further type-specific parameters are possible.
	 * @prop component The vue-component this answer-type relies on
	 * @prop icon The icon corresponding to this answer-type
	 * @prop label The answer-type label, that users will see as answer-type.
	 * @prop SYNC predefined This AnswerType has/needs predefined Options.
	 * @prop validate *optional* Define conditions where this question is not ok
	 *
	 * @prop titlePlaceholder The placeholder users see as empty question-title in edit-mode
	 * @prop createPlaceholder *optional* The placeholder that is visible in edit-mode, to indicate a submission form-input field
	 * @prop submitPlaceholder *optional* The placeholder that is visible in submit-mode, to indicate a form input-field
	 * @prop warningInvalid The warning users see in edit mode, if the question is invalid.
	 */

	multiple: {
		component: QuestionMultiple,
		icon: 'icon-answer-multiple',
		// TRANSLATORS Take care, a translation by word might not match! The english called 'Multiple-Choice' only allows to select a single-option (basically single-choice)!
		label: t('forms', 'Multiple choice'),
		predefined: true,

		// Using the same vue-component as multiple, this specifies that the component renders as multiple_unique.
		unique: true,
	},

	short: {
		component: QuestionShort,
		icon: 'icon-answer-short',
		label: t('forms', 'Short answer'),
		predefined: false,

		titlePlaceholder: t('forms', 'Title'),
		warningInvalid: t('forms', 'A title is required!'),
	},

	long: {
		component: QuestionLong,
		icon: 'icon-answer-long',
		label: t('forms', 'Long text'),
		predefined: false,

		titlePlaceholder: t('forms', 'Long text question title'),
		createPlaceholder: t('forms', 'People can enter a long text'),
		submitPlaceholder: t('forms', 'Enter a long text'),
		warningInvalid: t('forms', 'This question needs a title!'),
	},

	date: {
		component: QuestionDate,
		icon: 'icon-answer-date',
		label: t('forms', 'Date'),
		predefined: false,

		titlePlaceholder: t('forms', 'Date question title'),
		createPlaceholder: t('forms', 'People can pick a date'),
		submitPlaceholder: t('forms', 'Pick a date'),
		warningInvalid: t('forms', 'This question needs a title!'),
	},

	datetime: {
		component: QuestionDate,
		icon: 'icon-answer-datetime',
		label: t('forms', 'Datetime'),
		predefined: false,

		titlePlaceholder: t('forms', 'Datetime question title'),
		createPlaceholder: t('forms', 'People can pick a date and time'),
		submitPlaceholder: t('forms', 'Pick a date and time'),
		warningInvalid: t('forms', 'This question needs a title!'),

		// Using the same vue-component as date, this specifies that the component renders as datetime.
		includeTime: true,
	},
}
