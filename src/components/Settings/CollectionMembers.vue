<template>
	<div>
		<ul>
			<NcListItem v-for="member in members"
				:key="member.id"
				:name="member.userId"
				:bold="false"
				:active="true"
				class="active">
				<template #icon>
					<NcAvatar :size="44" :user="member.userId" />
				</template>
			</NcListItem>
		</ul>
	</div>
</template>

<script>
import { mapStores } from "pinia";

import NcListItem from "@nextcloud/vue/dist/Components/NcListItem.js";
import NcAvatar from "@nextcloud/vue/dist/Components/NcAvatar.js";

import { useSettingsStore } from "../../store/settings.js";
import { api } from "../../api.js";

export default {
	components: {
		NcListItem,
		NcAvatar,
	},
	data() {
		return {
			members: [],
		};
	},
	computed: {
		...mapStores(useSettingsStore),
	},
	mounted() {
		this.fetchMembers();
	},
	methods: {
		fetchMembers() {
			api.getCollectionMembers(this.settingsStore.context?.collectionId).then((result) => {
				this.members = result;
			});
		},
	},
};
</script>
