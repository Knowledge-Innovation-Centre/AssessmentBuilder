<template>
  <div class="aoat-bg-blue-300">
    <editor
      v-model="object.label"
      api-key="lho3l6oj8tvr7qmrxxmj3i4t8zwc6nk7h1ic415l2dyjzmt1"
      :init="{
        height: 200,
        menubar: false,
        convert_urls: false,
        plugins: [
          'advlist autolink lists link image charmap print preview anchor',
          'searchreplace visualblocks code fullscreen',
          'insertdatetime media table paste code help wordcount'
        ],
        toolbar:
          'undo redo | formatselect | bold italic backcolor | \
           alignleft aligncenter alignright alignjustify | \
           bullist numlist outdent indent | removeformat | help | image'
      }"
    />
  </div>
</template>

<script>
import Editor from "@tinymce/tinymce-vue";
export default {
  name: "Paragraph",
  components: {
    Editor
  },

  props: {
    object: {
      type: Object,
      required: true
    }
  },

  data() {
    return {};
  },
  methods: {}
};
</script>
<style scoped>
.background-grey {
  background: #eee;
}
</style>
