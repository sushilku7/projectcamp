<body>
    <span id="msg" style="color:red"></span><br/>
    <input type="file" id="photo"><br/>
  <script type="text/javascript" src="jquery-3.2.1.min.js"></script>
  <script type="text/javascript">
    $(document).ready(function(){
      $(document).on('change','#photo',function(){
        var property = document.getElementById('photo').files[0];
        var image_name = property.name;
        var image_extension = image_name.split('.').pop().toLowerCase();

        if(jQuery.inArray(image_extension,['gif','jpg','jpeg','']) == -1){
          alert("Invalid image file");
        }

        var form_data = new FormData();
        form_data.append("file",property);
        $.ajax({
          url:'upload.php',
          method:'POST',
          data:form_data,
          contentType:false,
          cache:false,
          processData:false,
          beforeSend:function(){
            $('#msg').html('Loading......');
          },
          success:function(data){
            console.log(data);
            $('#msg').html(data);
          }
        });
      });
    });
  </script>
</body>