<template>
  <div>
    <h5>{{ object.label }}</h5>
    <template v-if="media">
      <a :href="media.source_url" target="_blank">{{ media.title.rendered }}</a>
    </template>

    </div>
</template>

<script>
import Api from '../../Api'

  export default {

    name: 'TextReportElement',

    components: {

    },

    props: {
      object: {
        type: Object,
        required: true,
      }
    },

    computed: {
      value() {
        return this.$store.state.assessment[this.object.reportItemKey]
      }
    },

    data () {
      return {
        media: null
      };
    },

    mounted() {
      this.getMedia()
    },

    methods: {
      getMedia() {
        if (this.value) {
          Api.get(aoat_config.aoatGetMediaUrl + this.value).then(response => {
            this.media = response.data
          })
        }
      }
    }

  };
</script>
