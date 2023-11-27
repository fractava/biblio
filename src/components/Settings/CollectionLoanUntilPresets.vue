<template>
	<div>
		<table v-if="presets.length > 0"
			class="loanUntilPresetsTable">
			<colgroup>
				<col span="3">
				<col style="width: fit-content;">
			</colgroup>
			<thead>
				<tr>
					<th>{{ t("biblio", "Name") }}</th>
					<th>{{ t("biblio", "Type") }}</th>
					<th>{{ t("biblio", "Time") }}</th>
					<th>{{ t("biblio", "Actions") }}</th>
				</tr>
			</thead>
			<tbody>
				<LoanUntilPresetRow v-for="(preset, index) in presets"
					:key="preset.id"
					:preset.sync="presets[index]"
					@refresh="fetchPresets" />
			</tbody>
		</table>

		<NcEmptyContent v-if="presets.length === 0"
			:name="t('biblio', 'No presets')"
			style="margin-top: 20px; margin-bottom: 20px;">
			<template #icon>
				<GridOff />
			</template>
		</NcEmptyContent>

		<NcActions class="addPresetButton"
			primary
			:force-name="true">
			<NcActionButton @click="modalOpen = true">
				{{ t("biblio", "Add Preset") }}
				<template #icon>
					<Plus :size="20" />
				</template>
			</NcActionButton>
		</NcActions>

		<AddLoanUntilPresetModal :open.sync="modalOpen" @refresh="fetchPresets" />
	</div>
</template>

<script>
import { mapStores } from "pinia";

import NcActions from "@nextcloud/vue/dist/Components/NcActions.js";
import NcActionButton from "@nextcloud/vue/dist/Components/NcActionButton.js";
import NcEmptyContent from "@nextcloud/vue/dist/Components/NcEmptyContent.js"

import Plus from "vue-material-design-icons/Plus.vue";
import GridOff from "vue-material-design-icons/GridOff.vue";

import { useSettingsStore } from "../../store/settings.js";
import { api } from "../../api.js";
import LoanUntilPresetRow from "./LoanUntilPresetRow.vue";
import AddLoanUntilPresetModal from "./AddLoanUntilPresetModal.vue";

export default {
	components: {
		NcActions,
		NcActionButton,
		NcEmptyContent,
		Plus,
		GridOff,
		LoanUntilPresetRow,
		AddLoanUntilPresetModal,
	},
	data() {
		return {
			presets: [],
			modalOpen: false,
		};
	},
	computed: {
		...mapStores(useSettingsStore),
	},
	mounted() {
		this.fetchPresets();
	},
	methods: {
		fetchPresets() {
			api.getLoanUntilPresets(this.settingsStore.context?.collectionId).then((result) => {
				this.presets = result;
			});
		},
	},
};
</script>
<style lang="scss">
.loanUntilPresetsTable {
	width: 100%;
	border-collapse: collapse;
	margin-bottom: 20px;

	th {
		font-weight: bold;
	}

	td, th {
		padding: 7px;
		border-bottom: 1px solid var(--color-border);
	}

	tr:hover, tr:focus, tr:active {
		background-color: transparent;
	}
}
</style>
