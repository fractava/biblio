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
					@updated="refreshLoanUntilPresetsInBiblioStoreIfNeeded"
					@deleted="reFetch"/>
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

		<AddLoanUntilPresetModal :open.sync="modalOpen" @added="reFetch" />
	</div>
</template>

<script>
import { mapStores } from "pinia";
import debounceFn from "debounce-fn";

import NcActions from "@nextcloud/vue/dist/Components/NcActions.js";
import NcActionButton from "@nextcloud/vue/dist/Components/NcActionButton.js";
import NcEmptyContent from "@nextcloud/vue/dist/Components/NcEmptyContent.js"

import Plus from "vue-material-design-icons/Plus.vue";
import GridOff from "vue-material-design-icons/GridOff.vue";

import { useSettingsStore } from "../../store/settings.js";
import { useBiblioStore } from "../../store/biblio.js";

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
		reFetch() {
			this.fetchPresets();
			this.refreshLoanUntilPresetsInBiblioStoreIfNeeded();
		},
		refreshLoanUntilPresetsInBiblioStoreIfNeeded: debounceFn(function() {
			const biblioStore = useBiblioStore();

			// the settings made changes to the loan until presets of the collection currently selected in the main application
			// refresh the data, so the changes take effect in the main application without a manual refresh

			if (biblioStore.selectedCollectionId === this.settingsStore.context?.collectionId) {
				biblioStore.fetchLoanUntilPresets();
			}
		}, { wait: 2000 }),
	},
};
</script>
<style lang="scss" scoped>
.loanUntilPresetsTable {
	width: 100%;
	border-collapse: collapse;
	margin-bottom: 20px;

	th {
		font-weight: bold;
	}

	:deep(td), th {
		padding: 7px;
		border-bottom: 1px solid var(--color-border);
	}

	:deep(td) {
		vertical-align: bottom;
		padding-bottom: 13px;
	}

	:deep(td):last-child {
		text-align: center;
	}

	tr:hover, tr:focus, tr:active {
		background-color: transparent;
	}
}
.addPresetButton{
	width: 100% !important;
}
</style>
