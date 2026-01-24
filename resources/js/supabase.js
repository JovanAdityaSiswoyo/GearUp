import { createClient } from '@supabase/supabase-js';

const supabaseUrl = process.env.MIX_SUPABASE_URL || process.env.SUPABASE_URL;
const supabaseKey = process.env.MIX_SUPABASE_KEY || process.env.SUPABASE_KEY;

export const supabase = createClient(supabaseUrl, supabaseKey);

// Contoh query: Ambil data dari tabel 'products'
export async function getProducts() {
  const { data, error } = await supabase.from('products').select('*');
  if (error) throw error;
  return data;
}
