<template>
	<div>
		<div class="editModeContainer">
			<NcButton aria-label="Start/Stop editing mode"
				@click="editMode = !editMode">
				<template #icon>
					<Pencil :size="20" />
				</template>
				<template>Edit</template>
			</NcButton>
		</div>
		<div class="draggableTable">
			<div>
				<span>Titel:</span>
			</div>
			<div>
				<span>{{ title }}</span>
			</div>
		</div>
	</div>
</template>

<script>
import NcButton from '@nextcloud/vue/dist/Components/NcButton.js'

import Pencil from 'vue-material-design-icons/Pencil.vue';

export default {
	components: {
		NcButton,
		Pencil,
	},
	props: {
		title: {
			type: String,
			default: "",
		},
	},
	data() {
		return {
			editMode: false,
		};
	},
	methods: {
	},
};
</script>

<style scoped>

.editModeContainer {
	display: flex;
	justify-content: flex-end;
	margin-bottom: 20px;
}

.draggableTable {
	display: grid;
	grid-template-columns: 50% 50%;
}

.draggableTable > div {
	border: var(--color-text-maxcontrast) 2px solid;
    margin: -2px 0 0 -2px;
}
</style>
