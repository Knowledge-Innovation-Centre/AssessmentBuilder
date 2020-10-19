<template>
  <div>
    <div class="aoat-font-bold">{{ getLabel }}</div>
    <template v-if="files.length">
      <a class="" :key="media.ID" v-for="media in files" :href="media.source_url" target="_blank">
        {{ media.title.rendered }}
      </a>
    </template>

    <template v-else>
      <span>No files</span>
    </template>

  </div>
</template>

<script>
import Api from '../../Api'
import labelMixin from "./mixins/labelMixin";

  export default {

    name: 'TextReportElement',

    components: {

    },

    mixins: [
      labelMixin
    ],

    props: {
      object: {
        type: Object,
        required: true,
      }
    },

    data() {
      return {
        files: []
      }
    },

    computed: {
      value() {
        return this.$store.state.assessment[this.object.reportItemKey]
      },
    },

    mounted() {
      this.getMedia()
    },

    methods: {
      getMedia() {
        console.log(this.value);
        if (this.value) {
          for (let value of this.value) {
            Api.get(aoat_config.aoatGetMediaUrl + value).then(response => {
              this.files.push( response.data)
            })
          }
        }
      }
    }

  };
</script>
