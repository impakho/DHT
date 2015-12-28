#encoding: utf-8

import cmath
from BitVector import BitVector

class BloomFilter(object):
  def __init__(self, error_rate, elementNum):
    #计算所需要的bit数
    self.bit_num = -1 * elementNum * cmath.log(error_rate) / (cmath.log(2.0) * cmath.log(2.0))
    
    #四字节对齐
    self.bit_num = self.align_4byte(self.bit_num.real)
    
    #分配内存
    self.bit_array = BitVector(size=self.bit_num)
    
    #计算hash函数个数
    self.hash_num = cmath.log(2) * self.bit_num / elementNum
    
    self.hash_num = self.hash_num.real
    
    #向上取整
    self.hash_num = int(self.hash_num) + 1
    
    #产生hash函数种子
    self.hash_seeds = self.generate_hashseeds(self.hash_num)
    
  def insert_element(self, element):
    for seed in self.hash_seeds:
      hash_val = self.hash_element(element, seed)
      #取绝对值
      hash_val = abs(hash_val)
      #取模，防越界
      hash_val = hash_val % self.bit_num
      #设置相应的比特位
      self.bit_array[hash_val] = 1
  
  #检查元素是否存在，存在返回true，否则返回false 
  def is_element_exist(self, element):
    for seed in self.hash_seeds:
      hash_val = self.hash_element(element, seed)
      #取绝对值
      hash_val = abs(hash_val)
      #取模，防越界
      hash_val = hash_val % self.bit_num
      
      #查看值
      if self.bit_array[hash_val] == 0:
        return False
    return True
    
  #内存对齐	
  def align_4byte(self, bit_num):
    num = int(bit_num / 32)
    num = 32 * (num + 1)
    return num
  
  #产生hash函数种子,hash_num个素数
  def generate_hashseeds(self, hash_num):
    count = 0
    #连续两个种子的最小差值
    gap = 50
    #初始化hash种子为0
    hash_seeds = []
    for index in xrange(hash_num):
      hash_seeds.append(0)
    for index in xrange(10, 10000):
      max_num = int(cmath.sqrt(1.0 * index).real)
      flag = 1
      for num in xrange(2, max_num):
        if index % num == 0:
          flag = 0
          break
      
      if flag == 1:
        #连续两个hash种子的差值要大才行
        if count > 0 and (index - hash_seeds[count - 1]) < gap:
          continue
        hash_seeds[count] = index
        count = count + 1
      
      if count == hash_num:
        break
    return hash_seeds
  
  def hash_element(self, element, seed):
    hash_val = 1
    for ch in str(element):
      chval = ord(ch)
      hash_val = hash_val * seed + chval
    return hash_val


  def SaveBitToFile(self,f):
    self.bit_array.write_bits_to_fileobject(f)
    pass
