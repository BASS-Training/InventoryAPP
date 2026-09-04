import { useMemo, useState } from 'react';
import '../css/viewer-inventory-dashboard.css';

export default function ViewerInventoryDashboard({ data }) {
    const { categories, overview, requestSummary, recentRequests, quickLinks } = data;
    const [keyword, setKeyword] = useState('');
    const [selectedCategory, setSelectedCategory] = useState('all');
    const categoryThemes = ['emerald', 'violet', 'orange', 'rose', 'cyan', 'slate'];

    const filteredCategories = useMemo(() => {
        const term = keyword.trim().toLocaleLowerCase('id-ID');

        return categories
            .filter((category) => selectedCategory === 'all' || String(category.id) === selectedCategory)
            .map((category) => ({
                ...category,
                items: category.items.filter((item) => {
                    if (!term) return true;
                    return [item.name, item.code, item.type, item.location]
                        .filter(Boolean)
                        .some((value) => value.toLocaleLowerCase('id-ID').includes(term));
                }),
            }))
            .filter((category) => category.items.length > 0 || (!term && selectedCategory !== 'all'));
    }, [categories, keyword, selectedCategory]);

    const itemCount = filteredCategories.reduce((total, category) => total + category.items.length, 0);
    const lowStockItems = categories.flatMap((category) => category.items.filter((item) => item.minimumStock > 0 && item.stock <= item.minimumStock));
    const requestStatusThemes = { Diajukan: 'warning', Disetujui: 'primary', Diproses: 'info', Ditolak: 'danger', Selesai: 'success' };

    return (
        <div className="container-fluid viewer-inventory py-4">
            <header className="viewer-inventory__header mb-4">
                <div>
                    <p className="text-primary fw-semibold text-uppercase small mb-1">Inventaris</p>
                    <h1 className="h3 mb-1">Katalog Barang</h1>
                    <p className="text-muted mb-0">Cari dan lihat ketersediaan barang berdasarkan kategori.</p>
                </div>
                <div className="viewer-inventory__count"><strong>{itemCount}</strong><span>barang ditampilkan</span></div>
            </header>

            <section className="row g-3 mb-4" aria-label="Ringkasan inventaris">
                {overview.map((statistic) => <div className="col-6 col-xl-3" key={statistic.label}><div className={`viewer-inventory__stat viewer-inventory__stat--${statistic.theme}`}><i className={`bi ${statistic.icon}`} aria-hidden="true" /><div><strong>{statistic.value}</strong><span>{statistic.label}</span></div></div></div>)}
            </section>

            {lowStockItems.length > 0 && (
                <div className="alert alert-warning viewer-inventory__low-stock mb-4" role="status">
                    <i className="bi bi-exclamation-triangle-fill" aria-hidden="true" />
                    <span><strong>{lowStockItems.length} barang stok menipis.</strong> Informasikan kepada Staf Gudang agar ketersediaan dapat diperiksa atau ditambah.</span>
                </div>
            )}

            <section className="row g-4 mb-4">
                <div className="col-12 col-xl-5">
                    <div className="card viewer-inventory__panel h-100">
                        <div className="card-body"><div className="d-flex justify-content-between align-items-center mb-3"><div><h2 className="h5 mb-1">Status Pengajuan Saya</h2><p className="text-muted small mb-0">Perkembangan pengajuan dari akun Anda.</p></div><i className="bi bi-clipboard-data viewer-inventory__panel-icon" aria-hidden="true" /></div>
                            <div className="viewer-inventory__request-statuses">{requestSummary.map((summary) => <div key={summary.status} className="viewer-inventory__request-status"><span className={`badge text-bg-${requestStatusThemes[summary.status]}`}>{summary.status}</span><strong>{summary.count}</strong></div>)}</div>
                        </div>
                    </div>
                </div>
                <div className="col-12 col-xl-7">
                    <div className="card viewer-inventory__panel h-100"><div className="card-body"><div className="d-flex justify-content-between align-items-center mb-3"><div><h2 className="h5 mb-1">Pengajuan Terbaru</h2><p className="text-muted small mb-0">Lima pengajuan terakhir dari akun Anda.</p></div><a className="btn btn-sm btn-outline-primary" href={quickLinks.myRequests}>Lihat semua</a></div>
                        {recentRequests.length > 0 ? <div className="list-group list-group-flush">{recentRequests.map((request) => <div className="list-group-item px-0 d-flex justify-content-between align-items-center" key={request.id}><div><strong className="d-block">{request.item}</strong><small className="text-muted">{request.type} · {request.submittedAt}</small></div><span className={`badge text-bg-${requestStatusThemes[request.status] || 'secondary'}`}>{request.status}</span></div>)}</div> : <p className="text-muted mb-0">Belum ada pengajuan. Buat pengajuan saat Anda membutuhkan barang.</p>}
                    </div></div>
                </div>
            </section>

            <section className="viewer-inventory__quick-actions mb-4"><div><h2 className="h5 mb-1">Butuh barang?</h2><p className="mb-0 text-muted">Ajukan permintaan atau peminjaman melalui formulir yang tersedia.</p></div><div className="d-flex flex-wrap gap-2"><a className="btn btn-primary" href={quickLinks.newRequest}><i className="bi bi-plus-circle me-2" aria-hidden="true" />Buat Pengajuan</a><a className="btn btn-outline-primary" href={quickLinks.myRequests}>Pengajuan Saya</a></div></section>

            <div className="card viewer-inventory__filters mb-4">
                <div className="card-body row g-3 align-items-end">
                    <div className="col-12 col-md-7">
                        <label className="form-label" htmlFor="inventory-search">Cari barang</label>
                        <div className="input-group"><span className="input-group-text"><i className="bi bi-search" aria-hidden="true" /></span><input id="inventory-search" className="form-control" value={keyword} onChange={(event) => setKeyword(event.target.value)} placeholder="Nama, kode, jenis, atau lokasi..." /></div>
                    </div>
                    <div className="col-12 col-md-5">
                        <label className="form-label" htmlFor="inventory-category">Kategori</label>
                        <select id="inventory-category" className="form-select" value={selectedCategory} onChange={(event) => setSelectedCategory(event.target.value)}>
                            <option value="all">Semua kategori</option>
                            {categories.map((category) => <option key={category.id} value={category.id}>{category.name}</option>)}
                        </select>
                    </div>
                </div>
            </div>

            <div className="row g-4">
                {filteredCategories.map((category) => (
                    <div className="col-12 col-xl-6" key={category.id}>
                        <section className="card viewer-inventory__category h-100">
                            <div className={`card-header viewer-inventory__category-header viewer-inventory__category-header--${categoryThemes[(category.id - 1) % categoryThemes.length]} d-flex align-items-center justify-content-between`}><h2 className="h5 mb-0">{category.name}</h2><span className="badge rounded-pill text-bg-light">{category.items.length} barang</span></div>
                            <div className="table-responsive">
                                <table className="table table-hover mb-0">
                                    <thead><tr><th>Barang</th><th>Kode</th><th>Jenis</th><th>Lokasi</th><th className="text-center">Stok</th></tr></thead>
                                    <tbody>{category.items.map((item) => {
                                        const isLowStock = item.minimumStock > 0 && item.stock <= item.minimumStock;
                                        return <tr key={item.id} className={isLowStock ? 'table-warning' : ''}><td className="fw-semibold">{item.name}{isLowStock && <span className="viewer-inventory__low-label"><i className="bi bi-exclamation-circle-fill" aria-hidden="true" /> Stok menipis</span>}</td><td>{item.code || '-'}</td><td>{item.type}</td><td>{item.location || '-'}</td><td className="text-center"><span className={`badge ${isLowStock ? 'text-bg-warning' : 'text-bg-secondary'}`}>{item.stock} {item.unit || ''}</span>{isLowStock && <small className="d-block text-muted mt-1">Min. {item.minimumStock}</small>}</td></tr>;
                                    })}</tbody>
                                </table>
                            </div>
                        </section>
                    </div>
                ))}
            </div>
            {filteredCategories.length === 0 && <div className="alert alert-light border text-center py-4">Tidak ada barang yang sesuai dengan pencarian.</div>}
        </div>
    );
}
