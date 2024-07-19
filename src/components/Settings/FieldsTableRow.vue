<template>
	<tr class="tableRow">
		<!-- Drag handle -->
		<td class="drag-handle"
			:class="{'drag-handle-active': enableDragHandle}"
			:style="{'opacity': enableDragHandle ? .5 : 0}"
			:aria-label="t('biblio', 'Drag to reorder the fields')">
			<Drag :size="24" />
		</td>

		<!-- Icon -->
		<td>
			<slot name="icon" />
		</td>

		<!-- Include In List -->
		<td v-if="enableIncludeInList">
			<slot name="includeInList" />
		</td>

		<!-- Name -->
		<td>
			<slot name="name" />
		</td>

		<!-- Settings -->
		<td>
			<NcButton v-if="enableSettings"
				aria-label="Show Settings"
				@click="showModal">
				<template #icon>
					<Cog :size="20" />
				</template>
			</NcButton>

			<NcButton v-else
				aria-label="Show Settings"
				:disabled="true">
					<template #icon>
						<Cog :size="20" />
					</template>
			</NcButton>

			<NcModal v-if="settingsModalShown"
				ref="modalRef"
				@close="closeModal"
				name="Field Settings">
				<div style="margin: 50px;">
					<slot name="settings"></slot>
				</div>
			</NcModal>
		</td>

		<!-- Actions -->
		<td>
			<slot name="actions" />
		</td>
	</tr>
</template>

<script>
import NcButton from "@nextcloud/vue/dist/Components/NcButton.js";
import NcModal from "@nextcloud/vue/dist/Components/NcModal.js";

import Drag from "vue-material-design-icons/Drag.vue";
import Cog from "vue-material-design-icons/Cog.vue";

export default {
	components: {
		NcButton,
		NcModal,
		Drag,
		Cog,
	},
	data() {
		return {
			settingsModalShown: false,
		};
	},
	props: {
		enableDragHandle: {
			type: Boolean,
			default: true,
		},
		enableIncludeInList: {
			type: Boolean,
			default: true,
		},
		enableSettings: {
			type: Boolean,
			default: true,
		},
	},
	methods: {
		showModal() {
			this.settingsModalShown = true
		},
		closeModal() {
			this.settingsModalShown = false
		}
	},
};
</script>

<style lang="scss">
.fieldsTable .tableRow.ghost {
	opacity: 0;
}

.fieldsTable .tableRow.ghost td:not(:first-child) {
    padding-bottom: 4px;
}

.fieldsTable .tableRow.sortable-chosen ~ .tableRow {
    position: relative;
    top: -1px;
}

.fieldsTable:has(.tableRow.sortable-chosen ~ .tableRow) {
    margin-bottom: -1px;
}

/* Custom Border Collapse */
.tableRow td:not(:first-child) {
    background-color: var(--color-main-background);
    padding: 5px;
    border-left: 1px solid;
}
.tableRow td:last-child {
    border-right: 1px solid;
}

.tableRow:not(.ghost) td:not(:first-child), .tableRow.sortable-chosen td:not(:first-child) {
    border-top: 1px solid;
}

tbody:last-child .tableRow:not(.ghost):last-child td:not(:first-child), .tableRow.sortable-chosen td:not(:first-child), .tableRow:has(+ .tableRow.ghost) td:not(:first-child) {
    border-bottom: 1px solid;
}

.drag-handle {
    transition: opacity 0.5s;
}

.drag-handle-active {
    &:hover,
    &:focus {
        opacity: 1;
    }
    cursor: grab;

    &:active {
        cursor: grabbing;
    }
}
</style>
