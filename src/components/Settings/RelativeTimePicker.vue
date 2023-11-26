<template>
	<div class="container">
		<NcTextField class="relativeTextField"
			:value="timestampConvertedToUnit.toString()"
			:type="'number'"
			:label="selectedUnit.label"
			:placeholder="''"
			@update:value="updateTimestamp" />
		<vueSelect class="unitSelect"
			:options="units"
			:value="selectedUnit"
			:placeholder="t('biblio', 'Select Unit')"
			:clearable="false"
			@input="updateUnit" />
	</div>
</template>
<script>
import vueSelect from "vue-select";

import NcTextField from "@nextcloud/vue/dist/Components/NcTextField.js";

export default {
	components: {
		vueSelect,
		NcTextField,
	},
	props: {
		timestamp: {
			type: Number,
			default: 0,
		},
	},
	data() {
		return {
			units: [
				{
					id: "years",
					label: t("biblio", "Years"),
					factor: 86400 * 365,
				},
				{
					id: "days",
					label: t("biblio", "Days"),
					factor: 86400,
				},
				{
					id: "hours",
					label: t("biblio", "Hours"),
					factor: 3600,
				},
				{
					id: "seconds",
					label: t("biblio", "Seconds"),
					factor: 1,
				},
			],
			selectedUnit: {},
		};
	},
	computed: {
		timestampConvertedToUnit() {
			return this.timestamp / this.selectedUnit.factor;
		},
	},
	mounted() {
		this.selectedUnit = this.fittingUnit(this.timestamp);
	},
	methods: {
		fittingUnit(timestamp) {
			for (const unit of this.units) {
				if (timestamp % unit.factor === 0) {
					return unit;
				}
			}

			// cannot be reached, as long as one unit has a factor of one
			return false;
		},
		updateTimestamp(timestamp) {
			this.$emit("update:timestamp", parseInt(timestamp) * this.selectedUnit.factor);
		},
		updateUnit(unit) {
			const oldUnitConvertedTimestamp = this.timestampConvertedToUnit;
			this.selectedUnit = unit;
			this.updateTimestamp(oldUnitConvertedTimestamp);
		},
	},
};
</script>
<style lang="scss" scoped>
.container {
	display: flex;
	flex-wrap: wrap;
	align-items: center;

	.relativeTextField {
		flex-grow: 1;
		max-width: 100%;
		width: auto;
		margin-right: 5px;
	}

	.unitSelect {
		min-width: 100px;

		/*&.vs--open {
			:deep(.vs__selected-options) {
				padding-right: 16px;
			}
		}*/
	}
}
</style>
