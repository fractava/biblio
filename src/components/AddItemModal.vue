<template>
	<div>
		<NcButton icon="add"
			type="secondary"
			@click="showModal">
			{{ t('biblio', 'New Item') }}
			<template #icon>
				<Plus :size="20" />
			</template>
		</NcButton>
		<NcModal v-if="modalOpen"
			:name="t('biblio', 'Add new Item')"
			@close="closeModal">
			<div class="modal__content">
				<h2>{{ t('biblio', 'Add new Item') }}</h2>

				<NcTextField label="Title" :value.sync="title" />

				<NcButton :disabled="!title"
					type="primary"
					style="margin-top: 15px;"
					@click="submit">
					Submit
					<template #icon>
						<Plus v-if="!loading" :size="20" />
						<NcLoadingIcon v-else />
					</template>
				</NcButton>
			</div>
		</NcModal>
	</div>
</template>

<script>
import { mapStores } from "pinia";

import NcButton from "@nextcloud/vue/dist/Components/NcButton.js";
import NcModal from "@nextcloud/vue/dist/Components/NcModal.js";
import NcTextField from "@nextcloud/vue/dist/Components/NcTextField.js";
import NcLoadingIcon from '@nextcloud/vue/dist/Components/NcLoadingIcon.js';

import Plus from "vue-material-design-icons/Plus";

import { useBiblioStore } from "../store/biblio.js";

export default {
	components: {
		NcButton,
		NcModal,
		NcTextField,
		NcLoadingIcon,
		Plus,
	},
	data() {
		return {
			loading: false,
			modalOpen: false,
			title: "",
		};
	},
	computed: {
		...mapStores(useBiblioStore),
	},
	methods: {
		showModal() {
			this.modalOpen = true;
		},
		closeModal() {
			this.modalOpen = false;
		},
		async submit() {
			this.loading = true;
			try {
				await this.biblioStore.createItem({
					title: this.title,
				});
			} catch {

			} finally {
				this.loading = false;
				this.closeModal();
			}
		},
	},
};
</script>
<style scoped>
.modal__content {
	margin: 50px;
}

.modal__content h2 {
	text-align: center;
}
</style>