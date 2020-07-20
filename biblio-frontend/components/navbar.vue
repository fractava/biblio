<template>
	<div>
		<md-tabs md-sync-route>
			<md-tab id="tab-home" md-label="Medien E/A" to="" exact></md-tab>
			<md-tab id="tab-catalog" md-label="Katalogisierung" to=""></md-tab>
			<md-tab id="tab-students" md-label="Schüler" to=""></md-tab>
			<md-tab id="tab-lists" md-label="Listen" to=""></md-tab>
			<md-tab id="tab-admin" md-label="Verwaltung" to=""></md-tab>
		</md-tabs>
	</div>
</template>

<script>
export default {
	name: "navbar"
};
</script>
