import React, { useState, useEffect } from 'react';

export default function KpiItemForm({ kpiId, perspectives, oldData = {} }) {
    const [method, setMethod] = useState(oldData.calculation_method || 'positive');
    const [polaritas, setPolaritas] = useState(oldData.polaritas || 'Max');

    // Automatically set polaritas based on method
    useEffect(() => {
        if (method === 'negative') {
            setPolaritas('Min');
        } else {
            setPolaritas('Max');
        }
    }, [method]);

    const commonInputClass = "w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition";

    return (
        <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
            {/* Hidden Inputs for Form Submission */}
            <input type="hidden" name="polaritas" value={polaritas} />

            {/* KRA */}
            <div className="md:col-span-2">
                <label className="block text-xs font-semibold text-slate-500 uppercase mb-1">Area Kinerja Utama (KRA)</label>
                <input type="text" name="key_result_area" className={commonInputClass} defaultValue={oldData.key_result_area} placeholder="Contoh: Peningkatan Revenue" required />
            </div>

            {/* KPI */}
            <div className="md:col-span-2">
                <label className="block text-xs font-semibold text-slate-500 uppercase mb-1">Indikator Kinerja (KPI)</label>
                <textarea name="key_performance_indicator" className={commonInputClass} rows="2" defaultValue={oldData.key_performance_indicator} placeholder="Detail KPI..." required></textarea>
            </div>

            {/* METHOD SELECTION (Card Style) */}
            <div className="md:col-span-2">
                <label className="block text-xs font-semibold text-slate-500 uppercase mb-2">Metode Perhitungan</label>
                <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <label className={`cursor-pointer p-3 border rounded-lg flex flex-col gap-1 transition-all ${method === 'positive' ? 'border-emerald-500 bg-emerald-50 ring-1 ring-emerald-500' : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50'}`}>
                        <input type="radio" name="calculation_method" value="positive" checked={method === 'positive'} onChange={() => setMethod('positive')} className="hidden" />
                        <div className="flex items-center gap-2 font-bold text-slate-700 text-sm">
                            <i className="fas fa-chart-line text-emerald-500"></i> Target Positif
                        </div>
                        <div className="text-[10px] text-slate-500 leading-tight">Semakin tinggi realisasi, semakin baik skornya. Cocok untuk Volume Produksi.</div>
                    </label>

                    <label className={`cursor-pointer p-3 border rounded-lg flex flex-col gap-1 transition-all ${method === 'negative' ? 'border-rose-500 bg-rose-50 ring-1 ring-rose-500' : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50'}`}>
                        <input type="radio" name="calculation_method" value="negative" checked={method === 'negative'} onChange={() => setMethod('negative')} className="hidden" />
                        <div className="flex items-center gap-2 font-bold text-slate-700 text-sm">
                            <i className="fas fa-exclamation-triangle text-rose-500"></i> Target Negatif
                        </div>
                        <div className="text-[10px] text-slate-500 leading-tight">Semakin rendah realisasi, semakin baik skornya. Cocok untuk keterlambatan atau jumlah reject.</div>
                    </label>
                    <label className={`cursor-pointer p-3 border rounded-lg flex flex-col gap-1 transition-all ${method === 'progress' ? 'border-sky-500 bg-sky-50 ring-1 ring-sky-500' : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50'}`}>
                        <input type="radio" name="calculation_method" value="progress" checked={method === 'progress'} onChange={() => setMethod('progress')} className="hidden" />
                        <div className="flex items-center gap-2 font-bold text-slate-700 text-sm">
                            <i className="fas fa-tasks text-sky-500"></i> Progress Project
                        </div>
                        <div className="text-[10px] text-slate-500 leading-tight">Menghitung selisih kenaikan progres dari bulan sebelumnya. Cocok untuk proyek jangka panjang.</div>
                    </label>
                </div>
            </div>

            {/* PERSPEKTIF & BOBOT */}
            <div>
                <label className="block text-xs font-semibold text-slate-500 uppercase mb-1">Perspektif</label>
                <select name="perspektif" className={commonInputClass} defaultValue={oldData.perspektif}>
                    {perspectives.length === 0 ? <option value="">Default</option> : perspectives.map(p => <option key={p} value={p}>{p}</option>)}
                </select>
            </div>
            <div>
                <label className="block text-xs font-semibold text-slate-500 uppercase mb-1">Bobot (%)</label>
                <div className="relative">
                    <input type="number" step="0.01" name="bobot" className={`${commonInputClass} pl-3 pr-8`} defaultValue={oldData.bobot} required />
                    <span className="absolute right-3 top-2 text-slate-400 text-sm">%</span>
                </div>
            </div>

            {/* DYNAMIC TARGET & UNITS */}
            <div>
                <label className="block text-xs font-semibold text-slate-500 uppercase mb-1">Satuan (Units)</label>
                <input type="text" name="units" className={commonInputClass} defaultValue={oldData.units} placeholder={method === 'positive' ? 'Ex: Rp, Pcs' : method === 'negative' ? 'Ex: Kasus, Menit' : 'Ex: %'} required />
            </div>

            {/* TARGET DISPLAY BASED ON METHOD */}
            <div>
                {method === 'positive' && (
                    <>
                        <label className="block text-xs font-semibold text-slate-500 uppercase mb-1">Target Tahunan</label>
                        <input type="number" step="0.01" name="target" className={commonInputClass + " border-emerald-300 focus:ring-emerald-500"} defaultValue={oldData.target} required />
                    </>
                )}
                {method === 'negative' && (
                    <>
                        <label className="block text-xs font-semibold text-slate-500 uppercase mb-1">Batas Max (Target)</label>
                        <input type="number" step="0.01" name="target" className={commonInputClass + " border-rose-300 focus:ring-rose-500"} defaultValue={oldData.target || 0} placeholder="0 = Zero Defect" required />
                        <p className="text-[10px] text-slate-500 mt-1">Isi 0 jika target adalah nol kesalahan.</p>
                    </>
                )}
                {method === 'progress' && (
                    <>
                        <label className="block text-xs font-semibold text-slate-500 uppercase mb-1">Total Target Project</label>
                        <input type="number" name="target" className={commonInputClass + " bg-slate-100 text-slate-500"} value="100" readOnly />

                        <div className="mt-2">
                            <label className="block text-xs font-semibold text-slate-500 uppercase mb-1">Progress Awal (Carry Over)</label>
                            <div className="relative">
                                <input type="number" step="0.01" name="previous_progress" className={commonInputClass} defaultValue={oldData.previous_progress || 0} />
                                <span className="absolute right-3 top-2 text-slate-400 text-sm">%</span>
                            </div>
                            <p className="text-[10px] text-slate-500 mt-1">Diisi jika project sudah berjalan sebelumnya (misal 40%).</p>
                        </div>
                    </>
                )}
            </div>
        </div>
    );
}
