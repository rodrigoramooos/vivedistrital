  <!-- Bootstrap 5 JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- $additionalScripts para que as páginas possam adicionar scripts específicos, este está definido em cada página -->
  <?php if (isset($additionalScripts)): ?>
  <?php echo $additionalScripts; ?>
  <?php endif; ?>
</body>
</html>
