<footer class="main-footer">
  <div class="pull-right text-muted" id="load-time"></div>
  <strong>&copy; CV. MULTITECH INDONESIA</strong>
</footer>
</div>
<script type="text/javascript">
  $(window).load(function() {
    $('#load-time').html('<i class="fa fa-clock-o"></i> Page load time : ' + (Date.now() - timerStart) + ' millisecond');
  });
</script>
<script>
  $(function() {
    $('#example1').DataTable()
    $('#example3').DataTable()
    $('#example2').DataTable({
      'paging': true,
      'lengthChange': false,
      'searching': false,
      'ordering': true,
      'info': true,
      'autoWidth': false
    })
  })
</script>
</body>

</html>