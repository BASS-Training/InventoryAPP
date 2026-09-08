import { useState } from 'react';
import '../css/operations-dashboard.css';

function RequestList({ items, empty }) {
    return items.length ? <div className="ops-list">{items.map(item =>
        <a key={item.id} href={item.url} className="ops-request">
            <div><strong>{item.item}</strong><small>{item.person} · #{item.id}{item.due ? ' · Kembali ' + item.due : ''}</small></div>
            <span className={'ops-status ' + (item.status === 'Disetujui' ? 'approved' : '')}>{item.status}</span>
        </a>)}</div> : <p className="ops-empty">{empty}</p>;
}

export default function OperationsDashboard({ data }) {
    const [period, setPeriod] = useState(6);
    const months = data.months.slice(-period);
    const max = Math.max(1, ...months.flatMap(m => [m.incoming, m.outgoing]));
    const categoryTotal = data.categories.reduce((sum, c) => sum + c.value, 0);
    return <div className="ops">
        <header className="ops-hero">
            <div><div className="ops-eyebrow">{data.admin ? 'ADMIN / INVENTORY OVERVIEW' : 'GUDANG / DAILY OPERATIONS'}</div>
                <h1>{data.admin ? 'Inventaris dalam kendali.' : 'Siap untuk hari ini.'}</h1>
                <p>Halo, {data.name}. {data.admin ? 'Pantau kondisi inventaris dan kebutuhan tim dalam satu tempat.' : 'Cek antrean, siapkan barang, dan pastikan pinjaman kembali tepat waktu.'}</p>
                <div className="ops-actions">{data.links.incoming && <a href={data.links.incoming}>+ Catat stok masuk</a>}{data.links.queue && <a href={data.links.queue} className="secondary">Kelola pengajuan →</a>}</div>
            </div><div className="ops-date"><span>RINGKASAN HARI INI</span><strong>{data.date}</strong><small>Data diperbarui saat halaman dimuat</small></div>
        </header>
        <section className="ops-stats">{data.stats.map((s, i) => <article key={s.label}><span className="ops-number">0{i + 1}</span><p>{s.label}</p><strong>{s.value}</strong><small>{s.hint}</small></article>)}</section>
        <div className="ops-grid">
            <section className="ops-card ops-wide"><div className="ops-heading"><div><span className="ops-eyebrow">AKTIVITAS INVENTARIS</span><h2>Arus barang</h2></div><select aria-label="Periode grafik" value={period} onChange={e => setPeriod(Number(e.target.value))}><option value={6}>6 bulan terakhir</option><option value={3}>3 bulan terakhir</option></select></div>
                <p className="ops-muted">Jumlah transaksi masuk (termasuk pengembalian) dan keluar. Koreksi stok tidak dihitung.</p>
                <div className="ops-legend"><span>● Masuk</span><span>● Keluar</span></div>
                <div className="ops-chart" role="img" aria-label={months.map(m => m.label + ': masuk ' + m.incoming + ', keluar ' + m.outgoing).join('; ')}>
                    {months.map(m => <div className="ops-month" key={m.label}><div className="ops-bars"><div style={{height: Math.max(2, m.incoming / max * 140) + 'px'}}><span>{m.incoming}</span></div><div style={{height: Math.max(2, m.outgoing / max * 140) + 'px'}}><span>{m.outgoing}</span></div></div><small>{m.label}</small></div>)}
                </div>
                {months.every(m => !m.incoming && !m.outgoing) && <p className="ops-muted">Belum ada transaksi pada periode ini.</p>}
            </section>
            <section className="ops-card"><span className="ops-eyebrow">KOMPOSISI KATALOG</span><h2>Barang per kategori</h2><p className="ops-muted">Jumlah jenis barang, bukan penjumlahan stok berbeda satuan.</p>
                <div className="ops-categories">{data.categories.map((c, i) => <div key={c.label}><div><span>{c.label}</span><strong>{c.value}</strong></div><progress aria-label={c.label} value={c.value} max={categoryTotal || 1} className={'color-' + i % 3}/></div>)}</div>
                {!data.categories.length && <p className="ops-empty">Belum ada kategori.</p>}
            </section>
            <section className="ops-card ops-wide"><div className="ops-heading"><div><span className="ops-eyebrow">TINDAK LANJUT</span><h2>Antrean tim</h2></div><span className="ops-pill">Terlebih dahulu masuk</span></div><p className="ops-muted">Pengajuan orang lain yang menunggu approval atau serah-terima.</p><RequestList items={data.queue} empty="Antrean bersih. Belum ada pengajuan yang perlu ditangani." /></section>
            <section className="ops-card"><span className="ops-eyebrow ops-warning">PERLU PERHATIAN</span><h2>Prioritas restock</h2><div className="ops-list">{data.critical.map(b => <div className="ops-stock" key={b.id}><div>{b.url ? <a href={b.url}>{b.name}</a> : <strong>{b.name}</strong>}<small>Minimum {b.minimum} {b.unit}</small></div><strong className="ops-warning">{b.stock} {b.unit}</strong></div>)}</div>{!data.critical.length && <p className="ops-empty">Stok aktif berada di atas batas minimum.</p>}</section>
            <section className="ops-card ops-wide"><span className="ops-eyebrow ops-warning">PENGEMBALIAN</span><h2>Pinjaman lewat jatuh tempo</h2><RequestList items={data.overdue} empty="Tidak ada pinjaman yang melewati jatuh tempo." /></section>
            <section className="ops-card ops-personal"><span className="ops-eyebrow">KEBUTUHAN SAYA</span><h2>Anda juga bisa mengajukan.</h2><p className="ops-muted">Pengajuan pribadi ditangani Staf Gudang lain atau Admin.</p><div className="ops-actions">{data.links.request && <a href={data.links.request}>Minta barang</a>}{data.links.loan && <a href={data.links.loan}>Pinjam inventaris</a>}</div><RequestList items={data.personal} empty="Belum ada pengajuan pribadi." />{data.links.personal && <a href={data.links.personal}>Lihat pengajuan saya →</a>}</section>
        </div>
    </div>;
}
