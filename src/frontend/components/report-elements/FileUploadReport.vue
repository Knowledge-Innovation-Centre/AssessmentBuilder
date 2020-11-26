<template>
  <div>
    <div class="aoat-font-bold">{{ getLabel }}</div>
    <template v-if="files.length">
      <a
        v-for="media in files"
        :key="media.ID"
        class=""
        :href="media.source_url"
        target="_blank"
      >
        {{ media.title.rendered }}
      </a>
    </template>

    <template v-else>
      <span>No files</span>
    </template>
  </div>
</template>

<script>
import Api from "../../Api";
import labelMixin from "./mixins/labelMixin";

export default {
  name: "TextInputReport",

  components: {},

  mixins: [labelMixin],

  props: {
    object: {
      type: Object,
      required: true
    }
  },

  data() {
    return {
      files: []
    };
  },

  computed: {
    value() {
      return this.$store.state.assessment[this.object.reportItemKey];
    }
  },

  mounted() {
    this.getMedia();
  },

  methods: {
    getMedia() {
      if (this.value) {
        for (let value of this.value) {
          Api.get(aoat_config.aoatGetMediaUrl + value).then(response => {
            this.files.push(response.data);
          });
        }
      }
    }
  }
};
</script>
