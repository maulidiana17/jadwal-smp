   <form action="/siswa/{{ $siswa->nis }}/update" method="POST" id="forrmSiswa" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-12">
                    <div class="input-icon mb-3">
                        <span class="input-icon-addon">
                          <!-- Download SVG icon from http://tabler.io/icons/icon/user -->
                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-barcode">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7v-1a2 2 0 0 1 2 -2h2" /><path d="M4 17v1a2 2 0 0 0 2 2h2" /><path d="M16 4h2a2 2 0 0 1 2 2v1" /><path d="M16 20h2a2 2 0 0 0 2 -2v-1" /><path d="M5 11h1v2h-1z" /><path d="M10 11l0 2" />
                            <path d="M14 11h1v2h-1z" /><path d="M19 11l0 2" />
                        </svg>
                        </span>
                        <input type="text" readonly value="{{ $siswa->nis }}" id="nis" class="form-control ms-2" placeholder="NIS" name="nis">
                      </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="input-icon mb-3">
                        <span class="input-icon-addon">
                          <!-- Download SVG icon from http://tabler.io/icons/icon/user -->
                          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                            <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path>
                            <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                          </svg>
                        </span>
                        <input type="text" value="{{ $siswa->nama_lengkap }}" id="nama_lengkap" class="form-control ms-2" placeholder="Nama Lengkap" name="nama_lengkap">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="input-icon mb-3">
                        <span class="input-icon-addon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-school">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M22 9l-10 -4l-10 4l10 4l10 -4v6" />
                                <path d="M6 10.6v5.4a6 3 0 0 0 12 0v-5.4" />
                            </svg>
                        </span>
                      <select name="kelas" id="kelas" class="form-control ms-2">
                            <option value="">Pilih Kelas</option>
                            @foreach (['7', '8', '9'] as $tingkat)
                                <optgroup label="Kelas {{ $tingkat }}">
                                    @foreach(['A','B','C','D','E','F','G','H','I'] as $subkelas)
                                        <option value="{{ $tingkat }}{{ $subkelas }}"
                                            {{ $siswa->kelas === $tingkat . $subkelas ? 'selected' : '' }}>
                                            {{ $tingkat }}{{ $subkelas }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="input-icon mb-3">
                        <span class="input-icon-addon">
                          <!-- Download SVG icon from http://tabler.io/icons/icon/user -->
                          <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-phone">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2" /></svg>
                        </span>
                        <input type="text" value="{{ $siswa->no_hp }}" id="no_hp" class="form-control ms-2" placeholder="No. HP" name="no_hp">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="input-icon mb-3">
                        <span class="input-icon-addon">
                            <!-- Ikon Password -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-password">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M12 10v4" />
                                <path d="M10 13l4 -2" />
                                <path d="M10 11l4 2" />
                                <path d="M5 10v4" />
                                <path d="M3 13l4 -2" />
                                <path d="M3 11l4 2" />
                                <path d="M19 10v4" />
                                <path d="M17 13l4 -2" />
                                <path d="M17 11l4 2" />
                            </svg>
                        </span>
                        <input type="text" name="password" class="form-control ms-2" placeholder="Password Baru (Kosongkan jika tidak diubah)">
                    </div>
                </div>
            </div>
             <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="aktif" {{ $siswa->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="alumni" {{ $siswa->status == 'alumni' ? 'selected' : '' }}>Alumni</option>
                </select>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="form-label">Upload Foto <strong>.jpg .jpeg .png</strong>. Maksimal ukuran file 2KB.</div>
                    <input type="file" name="foto" class="form-control">
                    <input type="hidden" name="old_foto" value="{{ $siswa->foto }}" id="">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                  Close
                </button>
                <button class="btn btn-primary">
                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-send">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 14l11 -11" />
                        <path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5" />
                    </svg>
                    Simpan
                </button>
              </div>
        </form>

