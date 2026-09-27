import { Local } from '@/utils/storage.js';

// Kích thước component toàn cục
export const globalComponentSize = Local.get('themeConfigPrev')
  ? Local.get('themeConfigPrev').globalComponentSize
  : 'small';
