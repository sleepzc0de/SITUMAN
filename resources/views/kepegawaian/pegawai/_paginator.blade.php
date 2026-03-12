{{-- Partial: pagination links (dirender ulang via AJAX) --}}
@if($pegawai->hasPages())
{{ $pegawai->links() }}
@endif
