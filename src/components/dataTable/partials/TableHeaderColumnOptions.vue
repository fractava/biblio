<template>
	<div class="menu" :class="{showOnHover: !sortingByThisColumn}">
		<NcActions :open.sync="localOpenState" :force-menu="true">
			<template v-if="sortingByThisColumn" #icon>
				<SortDesc v-if="currentSortReverse" :size="20" />
				<SortAsc v-else-if="!currentSortReverse" :size="20" />
			</template>
			<template v-if="selectOperator">
				<NcActionButton @click="selectOperator = false">
					<template #icon>
						<ChevronLeft :size="25" />
					</template>
					{{ t('biblio', 'Back') }}
				</NcActionButton>
				<NcActionCaption :name="t('biblio', 'Select operator')" />
				<NcActionRadio v-for="(operator, index) in getOperators"
					:key="index"
					:name="'filter-operators-column-' + column.id"
					:value="operator.label"
					:checked="filterOperatorId === operator.id"
					@change="filterOperatorId = operator.id">
					{{ operator.label }}
				</NcActionRadio>
			</template>
			<template v-else-if="selectValue">
				<NcActionButton @click="selectValue = false">
					<template #icon>
						<ChevronLeft :size="25" />
					</template>
					{{ t('biblio', 'Back') }}
				</NcActionButton>
				<NcActionCaption :name="t('biblio', 'Select operand')" />
				<NcActionButton v-for="operand in column.filterOperandOptions"
					:key="operand.id"
					@click="filterOperand = operand.id">
					<template #icon>
						<FilterCog :size="25" />
					</template>
					{{ operand.label }}
				</NcActionButton>
			</template>
			<template v-else>
				<NcActionCaption v-if="canSort" :name="t('biblio', 'Sorting')" />
				<NcActionButtonGroup v-if="canSort">
					<NcActionButton :class="{ selected: sortingByThisColumn && !currentSortReverse }" :aria-label="t('biblio', 'Sort asc')" @click="sort(false)">
						<template #icon>
							<SortAsc :size="20" />
						</template>
					</NcActionButton>
					<NcActionButton :class="{ selected: sortingByThisColumn && currentSortReverse }" :aria-label="t('biblio', 'Sort desc')" @click="sort(true)">
						<template #icon>
							<SortDesc :size="20" />
						</template>
					</NcActionButton>
				</NcActionButtonGroup>
				<NcActionCaption v-if="showFilter && hasOperators" :name="t('biblio', 'Filtering')" />
				<NcActionButton v-if="showFilter && hasOperators"
					:name="filterOperator.label"
					@click="selectOperator = true">
					<template #icon>
						<FilterCog :size="25" />
					</template>
					{{ t('biblio', 'Select Operator') }}
				</NcActionButton>

				<!-- This is a options operand type, that needs it's own operand selection view, that this button opens -->
				<NcActionButton v-if="showFilter && column.filterOperandType === 'options' && hasOperators"
					@click="selectValue = true">
					<template #icon>
						<Magnify :size="25" />
					</template>
					{{ t('biblio', 'Select Operand') }}
				</NcActionButton>

				<!-- These are a string/date operand types, that don't needs it's own operand selection view -->
				<NcActionInput v-else-if="showFilter && column.filterOperandType === 'string'"
					:label-visible="false"
					:label="t('biblio', 'Input search')"
					:value.sync="filterOperand">
					<template #icon>
						<Magnify :size="20" />
					</template>
				</NcActionInput>
				<NcActionInput v-else-if="showFilter && column.filterOperandType === 'date'"
					type="datetime-local"
					is-native-picker
					:label-visible="false"
					:label="t('biblio', 'Input date')"
					:label-outside="false"
					:value="filterOperand"
					@input="dateInput">
					<template #icon>
						<Magnify :size="20" />
					</template>
				</NcActionInput>
			</template>
		</NcActions>
	</div>
</template>

<script>
import generalHelper from "../../mixins/generalHelper.js";
import SortAsc from "vue-material-design-icons/SortAscending.vue";
import SortDesc from "vue-material-design-icons/SortDescending.vue";
import ChevronLeft from "vue-material-design-icons/ChevronLeft.vue";
import FilterCog from "vue-material-design-icons/FilterCog.vue";
import Magnify from "vue-material-design-icons/Magnify.vue";
import NcActions from "@nextcloud/vue/dist/Components/NcActions.js";
import NcActionButton from "@nextcloud/vue/dist/Components/NcActionButton.js";
import NcActionInput from "@nextcloud/vue/dist/Components/NcActionInput.js";
import NcActionButtonGroup from "@nextcloud/vue/dist/Components/NcActionButtonGroup.js";
import NcActionCaption from "@nextcloud/vue/dist/Components/NcActionCaption.js";
import NcActionRadio from "@nextcloud/vue/dist/Components/NcActionRadio.js";

export default {
	components: {
		ChevronLeft,
		FilterCog,
		Magnify,
		NcActionInput,
		NcActionRadio,
		NcActionCaption,
		NcActionButton,
		NcActions,
		SortAsc,
		SortDesc,
		NcActionButtonGroup,
	},
	mixins: [generalHelper],
	props: {
		column: {
			type: Object,
			default: null,
		},
		openState: {
		      type: Boolean,
		      default: false,
		},
		currentSort: {
			type: String,
			default: "",
		},
		currentSortReverse: {
			type: Boolean,
			default: false,
		},
		currentFilters: {
			type: Object,
			default: () => ({}),
		},
	},
	data() {
		return {
			selectOperator: false,
			selectValue: false,
		};
	},
	computed: {
		sortingByThisColumn() {
			return this.currentSort === this.column.sortIdentifier;
		},
		getOperators() {
			return this.column.filterOperators;
		},
		hasOperators() {
			return this.getOperators.length > 0;
		},
		canSort() {
			return this.column.canSort;
		},
		showFilter() {
			return this.column.canFilter;
		},
		columnFilter() {
			return this.currentFilters[this.column.sortIdentifier] || {};
		},
		filterOperatorId: {
			get() {
				return this.columnFilter.operator || this.getOperators[0].id;
			},
			set(id) {
				this.patchColumnFilter({
					operatorId: id,
				});
			},
		},
		filterOperator: {
			get() {
				if (this.columnFilter.operator) {
					return this.getOperators.find((operator) => (operator.id === this.columnFilter.operator));
				} else {
					return this.getOperators[0];
				}
			},
			set(operator) {
				this.patchColumnFilter({
					operatorId: operator.id,
				});
			},
		},
		filterOperand: {
			get() {
				if (this.columnFilter.operand) {
					if (this.column.filterOperandType === "date") {
						return new Date(this.columnFilter.operand * 1000);
					} else {
						return this.columnFilter.operand;
					}
				} else {
					if (this.column.filterOperandType === "date") {
						return new Date();
					} else {
						return "";
					}
				}
			},
			set(operand) {
				this.patchColumnFilter({
					operand,
				});
			},
		},
		localOpenState: {
			get() {
				return this.openState;
			},
			set(v) {
				this.$emit("update:open-state", !!v);
			},
		},
	},
	watch: {
		localOpenState() {
			this.reset();
		},
	},
	created() {
		this.reset();
	},
	methods: {
		close() {
			this.localOpenState = false;
		},
		patchColumnFilter({ operatorId, operand }) {
			let newOperand;
			if (!operand && operand !== "") {
				newOperand = this.columnFilter?.operand || "";
			} else {
				newOperand = operand;
			}

			const patchedFilter = Object.assign({}, this.currentFilters[this.column.sortIdentifier], {
				operator: operatorId || this.columnFilter?.operator || this.getOperators[0].id,
				operand: newOperand,
			});

			const filters = Object.assign({}, this.currentFilters, {
				[this.column.sortIdentifier]: patchedFilter,
			});

			this.$emit("update:currentFilters", filters);
		},
		reset() {
			this.operator = null;
			this.selectOperator = false;
			this.selectValue = false;
		},
		sort(reverse) {
			if (this.currentSort === this.column.sortIdentifier && this.currentSortReverse === reverse) {
				this.$emit("update:currentSort", "");
				this.$emit("update:currentSortReverse", false);
			} else {
				this.$emit("update:currentSort", this.column.sortIdentifier);
				this.$emit("update:currentSortReverse", reverse);
			}
			this.close();
		},
		dateInput(value) {
			const unixTimestamp = Math.floor(new Date(value).valueOf() / 1000);
			this.filterOperand = unixTimestamp;
		},
	},
};
</script>
<style lang="scss" scoped>
.menu {
	padding-left: calc(var(--default-grid-baseline) * 1);
}

.selected {
	background-color: var(--color-primary-element-light) !important;
	border-radius: 6px;
}

.selected-option {
	width: 100%;
}
</style>
